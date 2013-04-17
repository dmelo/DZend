<?php

class DZend_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
    static protected $_transformFrom = array();
    static protected $_transformTo = array();
    protected $_logger;
    protected $_enableTransform;
    static protected $_columnNameArray = array();

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_enableTransform = true;
        $this->_logger = Zend_Registry::get('logger');
    }

    public function __toString()
    {
        return get_class($this) . ':: ' . print_r($this->toArray(), true);
    }

    protected function _transformColumn($columnName)
    {
        if (!is_string($columnName)) {
            throw new Zend_Db_Table_Row_Exception(
                'Specified column is not a string'
            );
        }

        if ($this->_enableTransform) {
            if (!array_key_exists($columnName, self::$_columnNameArray)) {
                if (count(self::$_transformFrom) == 0) {
                    foreach (range('a', 'z') as $letter) {
                        self::$_transformFrom[] = strtoupper($letter);
                        self::$_transformTo[] = '_' . $letter;
                    }
                }

                self::$_columnNameArray[$columnName] = str_replace(
                    self::$_transformFrom, self::$_transformTo, $columnName
                );
            }

            return self::$_columnNameArray[$columnName];
        } else {
            return $columnName;
        }
    }
}
