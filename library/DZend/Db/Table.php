<?php

class DZend_Db_Table extends Zend_Db_Table_Abstract
{
    use DZend_CurrentUser;

    protected $_db;
    protected $_primary = 'id';
    protected $_name;
    protected $_rowClass;
    protected $_logger;
    protected $_cache;
    protected $_allowRequestCache = false;
    protected $_section = 'ddb';
    static protected $_cacheHit = 0;
    static protected $_cacheMiss = 0;

    protected function _setupDatabaseAdapter()
    {
        $bootstrap = Zend_Controller_Front::getInstance()
            ->getParam('bootstrap');

        if (null !== $bootstrap &&
            null !== ($multidb = $bootstrap->getPluginResource('multidb'))) {
            $this->_db = $multidb->getDb($this->_section);
        } else {
            parent::_setupDatabaseAdapter();
        }
    }

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_logger = Zend_Registry::get('logger');
        $this->_db = $this->getAdapter();

        try {
            $names = Zend_Registry::get('DZend_Db_Table::_name');
        } catch (Zend_Exception $e) {
            $names = array();
        }

        $className = get_class($this);
        if (!array_key_exists($className, $names)) {
            $names[$className] = DZend_Db_Table::camelToUnderscore(
                preg_replace('/^DbTable_/', '', get_class($this))
            );
            Zend_Registry::set('DZend_Db_Table::_name', $names);
        }

        $this->_name = $names[$className]; // Its set by default to be get_class($this), by Zend.
        $this->_rowClass = isset($this->_rowClass) ? $this->_rowClass : get_class($this) . 'Row';
    }

    public function findRowById($id)
    {
        $ret = null;

        if ($this->_allowRequestCache) {
            $c = new DZend_Chronometer();
            $cName = get_class($this);
            $c->start();
            try {
                $cache = Zend_Registry::get($cName);
            } catch (Zend_Exception $e) {
                $cache = array();
            }

            $ret = null;
            if (array_key_exists($id, $cache) && null !== $cache[$id]) {
                $stats = "$cName::findRowById cache hit on $id";
                self::$_cacheHit++;
                $ret = $cache[$id];
            } else {
                $stats = "$cName::findRowById cache miss on $id";
                self::$_cacheMiss++;
                $ret = $this->findRowByIdWithoutCache($id);
                $cache[$id] = $ret;
                Zend_Registry::set($cName, $cache);
            }
            $c->stop();
            $this->_logger->debug(
                $stats . ' - ' . $c->get() . ' - cacheHit: '
                . self::$_cacheHit . ' - cacheMiss: ' . self::$_cacheMiss
            );
        } else {
            $ret = $this->findRowByIdWithoutCache($id);
        }

        return $ret;
    }

    public function findById($id)
    {
        $ret = null;
        if ($this->_allowRequestCache) {
            $c = new DZend_Chronometer();
            $c->start();
            $cName = get_class($this);
            $stats = '';
            $rows = array();
            if (is_array($id)) {
                try {
                    $cache = Zend_Registry::get($cName);
                } catch (Zend_Exception $e) {
                    $cache = array();
                }
                $cacheMissIds = array();
                foreach ($id as $i) {
                    if (array_key_exists($i, $cache)) {
                        $rows[] = $cache[$i];
                    } else {
                        $cacheMissIds[] = $i;
                    }
                }
                $stats = "findById cache hit on " . count($rows)
                    . " and missed on " . count($cacheMissIds) . ' elements';

                if (count($cacheMissIds) > 0) {
                    $l = $this->findByIdWithoutCache($cacheMissIds);
                    foreach ($l as $row) {
                        $cache[$row->id] = $row;
                        $rows[] = $row;
                    }
                    Zend_Registry::set($cName, $cache);
                }
            } else {
                $stats = 'reroute to findRowById';
                $rows = array($this->findRowById($id));
            }

            $ret = $rows;
            $c->stop();
            $this->_logger->debug($stats . ' - ' . $c->get());
        } else {
            $ret = $this->findByIdWithoutCache($id);
        }

        return $ret;
    }


    public static function camelToUnderscore($name)
    {
        $n = strtolower($name[0]);

        for ($i = 1; $i < strlen($name); $i++) {
            if (preg_match('/[A-Z]/', $name[$i]))
                $n .= '_' . strtolower($name[$i]);
            else
                $n .= $name[$i];
        }

        return $n;
    }

    public static function underscoreToCamel($name)
    {
        $list = explode("_", $name);
        $ret = "";
        foreach($list as $piece)
            $ret .= ucfirst($piece);

        return lcfirst($ret);
    }

    protected function _transform($items)
    {
        $ret = array();
        foreach ($items as $item)
            $ret[] = DZend_Db_Table::camelToUnderscore($item);

        return $ret;
    }

    protected function _funcToQuery($funcName, $args)
    {
        $funcName = preg_replace('/^(find(|Row)|delete)By/', '', $funcName);
        $items = explode('And', $funcName);
        $items = $this->_transform($items);
        $where = '';
        foreach ($items as $key => $item) {
            if ($key) {
                $where .= ' AND ';
            }

            if (null === $args[$key]) {
                $where .= $this->_db->quoteIdentifier($item) . ' is null';
            } elseif (is_array($args[$key])) {
                if (empty($args[$key])) {
                    throw new Zend_Exception('empty array');
                }

                $first = true;
                $where .= $this->_db->quoteIdentifier($item) . ' in ( ';
                foreach ($args[$key] as $i) {
                    if ($first) {
                        $first = false;
                    } else {
                        $where .= ',';
                    }
                    $where .= $this->_db->quoteInto(' ? ', $i);
                }
                $where .= ') ';
            } else {
                $where .= $this->_db->quoteInto($this->_db->quoteIdentifier($item) . ' = ?', $args[$key]);
            }
        }

        return $where;
    }

    public function findByDataSet($dataSet)
    {
        $where = '';
        $firstRow = true;
        foreach ($dataSet as $data) {
            if ($firstRow)
                $firstRow = false;
            else
                $where .= ' OR ';
            $where .= '(';
            $first = true;
            foreach ($data as $key => $value) {
                if ($first)
                    $first = false;
                else
                    $where .= ' AND ';
                $where .= $this->_db->quoteInto("$key = ?", $value);
            }
            $where .= ')';
        }

        return $this->fetchAll($where);
    }

    public function __call($funcName, $args)
    {
        $niddle = '/^(find(|Row)|delete)By/';
        if (preg_match($niddle, $funcName)) {
            try {
                $where = $this->_funcToQuery($funcName, $args);
            } catch (Zend_Exception $e) {
                $where = null;
            }
            if (preg_match('/^findBy.*/', $funcName)) {
                return null === $where ? array() : $this->fetchAll($where);
            } elseif (preg_match('/^findRowBy.*/', $funcName)) {
                return null === $where ? null : $this->fetchRow($where);
            } elseif (preg_match('/^deleteBy.*/', $funcName)) {
                return $this->delete($where);
            }
        }
    }

    public function insert(array $data)
    {
        return $this->_insert($data);
    }

    protected function _insert($data)
    {
        $c = new DZend_Chronometer();

        $c->start();
        $ret  = parent::insert($data);
        $c->stop();

        $this->_logger->debug(get_class($this) . '::insert - ' . $c->get());

        return $ret;
    }

    public function insertWithoutException($data)
    {
        $ret = null;
        try {
            $ret = $this->_insert($data);
        } catch(Zend_Db_Statement_Exception $e) {
            $funcName = '';
            $first = true;
            $i = 0;
            foreach ($data as $key => $value) {
                if($first)
                    $first = false;
                else
                    $funcName .= 'And';
                $funcName .= ucfirst($this->underscoreToCamel($key));
                $args[$i] = $value;
                $i++;
            }

            $where = $this->_funcToQuery($funcName, $args);
            $row = $this->fetchRow($where);

            if (null === $row) {
                $this->_logger->debug(
                    get_class($this) . '::insertWithoutException ERROR##' .
                    print_r($data, true) . '##' . $funcName .
                    '## returned null during search with where = ' . $where
                );

                throw $e;
            }

            $ret = $row->id;
        }

        return $ret;
    }

    public function getCacheKey($data)
    {
        return $this->_name . sha1(print_r($data, true));
    }

    public function insertCachedWithoutException($data)
    {
        $cache = Cache::get();
        $key = $this->getCacheKey($data);
        $ret = null;

        if (($ret = $cache->load($key)) === false) {
            $ret = $this->insertWithoutException($data);
            $cache->save($ret, $key);
        }

        return $ret;
    }

    private function _insertBatch($tmpData, $sql)
    {
        $savepointName = "insertBatch_" . get_class($this);
        try {
            $this->_db->query($sql);
            $this->_logger->debug("Inserted " . count($tmpData) . " rows");
        } catch (Exception $e) {
            $this->_logger->err(
                "Failed inserting " . count($tmpData) . " rows. Details: "
                . $e->getMessage() . PHP_EOL . $e->getTraceAsString()
                . '. Query was: ' . $sql
            );

            throw $e;
        }
    }
    
    public function insertMulti($dataSet, $batchSize = 50)
    {
        if (0 === count($dataSet)) {
            return;
        }

        $columnNames = array_keys($dataSet[0]);
        foreach ($columnNames as &$name) {
            $name = $this->_db->quoteIdentifier($name);
        }

        switch (get_class($this->_db)) {
            case 'Zend_Db_Adapter_Pdo_Mysql':
                $prefix = 'INSERT IGNORE INTO ';
                break;
            default:
                $prefix = 'INSERT INTO ';
        }

        $prefix .= $this->_db->quoteIdentifier($this->info('name')) . '('
            . implode(',', $columnNames) . ') VALUES';

        $sql = $prefix;
        $tmpData = array();
        foreach ($dataSet as $data) {
            if (count($tmpData) > 0) {
                $sql .= ',';
            }
            $sql .= '(';
            $first = true;
            foreach ($data as $value) {
                if ($first) {
                    $first = false;
                } else {
                    $sql .= ',';
                }
                $sql .= $this->_db->quoteInto('?', $value);
            }
            $sql .= ')';
            $tmpData[] = $data;

            if (count($tmpData) >= $batchSize) {
                $this->_insertBatch($tmpData, $sql);
                $sql = $prefix;
                $tmpData = array();
            }
        }

        if (count($tmpData) > 0) {
            $this->_insertBatch($tmpData, $sql);
        }
    }

    public function preload($ids)
    {
        $this->findById($ids);
    }

    public function findByIdWithoutCache($id)
    {
        $where = null;
        if (is_array($id)) {
            $where = 'id in (';
            $first = true;
            foreach ($id as $i) {
                if ($first) {
                    $first = false;
                } else {
                    $where .= ', ';
                }
                $where .= $this->_db->quoteInto('?', $i);
            }
            $where .= ')';
        } else {
            $where = $this->_db->quoteInto('id = ?', $id);
        }

        return $this->fetchAll($where);
    }

    public function findRowByIdWithoutCache($id)
    {
        return $this->fetchRow($this->_db->quoteInto('id = ?', $id));
    }

    public function getMaxId()
    {
        $column = $this->_primary;
        $row = $this->fetchRow(
            $this->select()->order("$column desc")
        );

        return (int) $row->$column;
    }
}
