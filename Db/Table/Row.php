<?php

class DZend_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
    protected $_transformFrom;
    protected $_transformTo;
    protected $_logger;
    protected $_enableTransform;
    protected $_columnNameArray;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_enableTransform = true;
        $this->_transformFrom = array();
        $this->_transformTo = array();
        $this->_columnNameArray = array();
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
            if (!array_key_exists($columnName, $this->_columnNameArray)) {
                if (count($this->_transformFrom) == 0) {
                    foreach (range('a', 'z') as $letter) {
                        $this->_transformFrom[] = strtoupper($letter);
                        $this->_transformTo[] = '_' . $letter;
                    }
                }

                $this->_columnNameArray[$columnName] = str_replace(
                    $this->_transformFrom, $this->_transformTo, $columnName
                );
            }

            return $this->_columnNameArray[$columnName];
        } else {
            return $columnName;
        }
    }
}
