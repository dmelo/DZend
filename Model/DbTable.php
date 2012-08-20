<?php

class DZend_Model_DbTable extends Zend_Db_Table_Abstract
{
    protected $_db;
    protected $_session;
    protected $_primary = 'id';
    protected $_name;
    protected $_rowClass;
    protected $_logger;
    protected $_cache;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_logger = Zend_Registry::get('logger');
        $this->_db = $this->getAdapter();
        $this->_session = DZend_Session_Namespace::get('session');
        $this->_name = DZend_Model_DbTable::camelToUnderscore(
            preg_replace('/^DbTable_/', '', get_class($this))
        );
        $this->_rowClass = get_class($this) . 'Row';
        $frontendOptions = array(
            'lifetime' => 30 * 24 * 60 * 60, // one month
            'automatic_serialization' => true
        );

        $backendOptions = array();

        $this->_cache = Zend_Cache::factory(
            'Core',
            'Apc',
            $frontendOptions,
            $backendOptions
        );

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
            $ret[] = DZend_Model_DbTable::camelToUnderscore($item);

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

            if (null === $args[$key])
                $where .= $item . ' is null';
            else
                $where .= $this->_db->quoteInto($item . ' = ?', $args[$key]);
        }

        return $where;
    }

    public function __call($funcName, $args)
    {
        if(is_array($args[0]))
            $args = $args[0];
        $where = $this->_funcToQuery($funcName, $args);
        if (preg_match('/^findBy.*/', $funcName)) {
            return $this->fetchAll($where);
        } elseif (preg_match('/^findRowBy.*/', $funcName)) {
            return $this->fetchRow($where);
        } elseif (preg_match('/^deleteBy.*/', $funcName)) {
            return $this->delete($where);
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
            $funcName = 'findRowBy';
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
            $row = $this->$funcName($args);

            if (null == $row) {
                $this->_logger->debug(
                    get_class() . '::insertWithoutException ERROR##' .
                    print_r($data, true) . '##' . $funcName .
                    '## returned null during search'
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
        $key = $this->getCacheKey($data);
        if (($ret = $this->_cache->load($key)) === false) {
            $ret = $this->insertWithoutException($data);
            $this->_cache->save($ret, $key);
        }

        return $ret;
    }
}
