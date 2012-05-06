<?php

class DZend_Model
{
    protected $_logger;

    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
    }
}
