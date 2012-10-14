<?php

class DZend_Db_Table_Row extends Zend_Db_Table_Row_Abstract
{
    protected $_transformFrom;
    protected $_transformTo;
    protected $_logger;
    protected $_enableTransform;

    public function __construct($config = array())
    {
        parent::__construct($config);

        $this->_enableTransform = true;
        $this->_transformFrom = array();
        $this->_transformTo = array();
        $this->_logger = Zend_Registry::get('logger');
    }

    protected function _transformColumn($columnName)
    {
        if (!is_string($columnName)) {
            throw new Zend_Db_Table_Row_Exception(
                'Specified column is not a string'
            );
        }

        if ($this->_enableTransform) {
            if (count($this->_transformFrom) == 0) {
                foreach (range('a', 'z') as $letter) {
                    $this->_transformFrom[] = strtoupper($letter);
                    $this->_transformTo[] = '_' . $letter;
                }
            }

            return str_replace(
                $this->_transformFrom, $this->_transformTo, $columnName
            );
        } else {
            return $columnName;
        }
    }
}
