<?php

class DZend_Db_Table extends Zend_Db_Table_Abstract
{
    protected $_db;
    protected $_session;
    protected $_primary = 'id';
    protected $_name;
    protected $_rowClass;
    protected $_logger;
    protected $_cache;
    protected $_hscache;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_logger = Zend_Registry::get('logger');
        $this->_db = $this->getAdapter();
        $this->_session = DZend_Session_Namespace::get('session');


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
        $this->_name = $names[$className];
        $this->_rowClass = get_class($this) . 'Row';

        $this->_hscache = Zend_Registry::get('hscache');

        
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
            if ($key)
                $where .= ' AND ';

            if (null === $args[$key]) {
                $where .= $item . ' is null';
            } elseif (is_array($args[$key])) {
                $first = true;
                $where .= $item . ' in ( ';
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
                $where .= $this->_db->quoteInto($item . ' = ?', $args[$key]);
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
            $where = $this->_funcToQuery($funcName, $args);
            if (preg_match('/^findBy.*/', $funcName)) {
                return $this->fetchAll($where);
            } elseif (preg_match('/^findRowBy.*/', $funcName)) {
                return $this->fetchRow($where);
            } elseif (preg_match('/^deleteBy.*/', $funcName)) {
                return $this->delete($where);
            }
        }
    }

    public function insert($data)
    {
        return $this->_insert($data);
    }

    protected function _insert($data)
    {
        $c = new DZend_Chronometer();

        $c->start();
        $ret  = parent::insert($data);
        $c->stop();

        $this->_logger->debug(get_class() . '::insert - ' . $c->get());

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

            if (null == $row) {
                $this->_logger->debug(
                    get_class() . '::insertWithoutException ERROR##' .
                    print_r($data, true) . '##' . $funcName .
                    '## returned null during search'
                );
                var_dump($e->getTrace());
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
        $ret = $this->insertWithoutException($data);

        return $ret;
    }

    /**
     * insertTree Insert multiple rows dibiding the data as a binary tree when
     * error occours during the insertion.
     *
     * @param mixed $dataSet
     * @return array An array with elements, the first is an int that says how
     * many database requests was performed and the second, also an int,
     * reporting how many rows was successfully inserted.
     */
    public function insertTree($dataSet, $depth = 0)
    {
        $this->_logger->debug(
            'DZend_Db_Table::insertTree count(dataset) ' . count($dataSet)
            . '. depth: ' . $depth
        );

        $ret = array(0, 0);
        if (0 !== count($dataSet)) {
            $db = $this->getAdapter();
            $sql = 'INSERT INTO ' . $this->info('name') . '('
                . implode(', ', array_keys($dataSet[0])) . ') VALUES ';
            $first = true;
            foreach ($dataSet as $data) {
                if ($first)
                    $first = false;
                else
                    $sql .= ', ';
                $sql .= '(' . implode(', ', $data) . ')';
            }

            try {
                $db->query($sql);

                $ret =  array(1, count($dataSet));
            } catch(Zend_Exception $e) {
                /*
                echo get_class($e) . PHP_EOL;
                echo $e->getMessage() . PHP_EOL;
                echo $e->getStack() . PHP_EOL;
                */

                $middle = (int) (count($dataSet) / 2);
                if ($middle > 0) {
                    $first = $this->insertTree(
                        array_slice($dataSet, 0, $middle), $depth + 1
                    );
                    $last = $this->insertTree(
                        array_slice(
                            $dataSet,
                            $middle,
                            count($dataSet) - $middle
                        ), $depth + 1
                    );

                    $ret = array(
                        $first[0] + $last[0] + 1, $first[1] + $last[1]
                    );
                } else
                    $ret = array(1, 0);
            }
        }

        return $ret;
    }

    public function insertMulti($dataSet, $bunchSize = 50)
    {
        $a = microtime(true);
        if (0 !== count($dataSet)) {
            $db = $this->getAdapter();
            $sqls = array();
            $i = 0;
            $index = 0;
            $sqls[0] = '';
            foreach ($dataSet as $data) {
                $sqls[$index] .= 'INSERT INTO ' . $this->info('name') . '('
                    . implode(', ', array_keys($dataSet[0])) . ') VALUES(';
                $first = true;
                foreach ($data as $value) {
                    if ($first)
                        $first = false;
                    else
                        $sqls[$index] .= ', ';
                    $sqls[$index] .= $this->_db->quoteInto('?', $value);
                }
                $sqls[$index] .=  '); ';
                $i++;
                if ($i === $bunchSize) {
                    $index++;
                    $sqls[$index] = '';
                    $i = 1;
                }
            }

            try {
                foreach ($sqls as $sql)
                    $db->query($sql);
            } catch(Zend_Exception $e) {
                $this->_logger->debug(get_class($e));
                $this->_logger->debug($e->getMessage());
                $this->_logger->debug($e->getStack());
            }
        }

        $b = microtime(true);
        $this->_logger->debug(
            "DZend_Db_Table::insertMulti time: " . ($b - $a) . ". count: "
            . count($dataSet) . ". table: " . $this->info('name')
        );
    }
}
