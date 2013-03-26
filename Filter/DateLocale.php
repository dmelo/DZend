<?php

require_once 'Zend/Filter/Interface.php';

class DZend_Filter_DateLocale implements Zend_Filter_Interface
{
    protected $_pattern;
    protected $_replacement;

    public function __construct($locale)
    {
        if ('pt-BR' === $locale) {
            $this->_pattern = '/(\d{2})\/(\d{2})\/(\d{4})/';
            $this->_replacement = '\3-\2-\1';
        }
    }

    public function filter($value)
    {
        return preg_replace($this->_pattern, $this->_replacement, $value);
    }
}
