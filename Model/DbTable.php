<?php

class DZend_Model_DbTable extends Zend_Db_Table_Abstract
{
    protected $_db;
    protected $_session;
    protected $_primary = 'id';
    protected $_name;
    protected $_rowClass;

    public function __construct($config = array())
    {
        parent::__construct($config);
        $this->_db = $this->getAdapter();
        $this->_session = DZend_Session_Namespace::get('session');
        $this->_name = DZend_Model_DbTable::camelToUnderscore(preg_replace('/^DbTable_/', '', get_class($this)));
        $this->_rowClass = get_class($this) . 'Row';
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

    public function insertWithoutException($data)
    {
        try {
            return parent::insert($data);
        } catch(Zend_Db_Exception $e) {
            $funcName = 'findRowBy';
            $first = true;
            $i = 0;
            foreach($data as $key => $value) {
                if($first)
                    $first = false;
                else
                    $funcName .= 'And';
                $funcName .= ucfirst($this->underscoreToCamel($key));
                $args[$i] = $value;
                $i++;
            }
            $row = $this->$funcName($args);
            return $row->id;
        }
    }
}
