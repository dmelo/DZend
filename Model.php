<?php

class DZend_Model
{
    protected $_logger;
    protected $_session;

    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
        try {
            $this->_session = DZend_Session_Namespace::get('session');
        } catch (Zend_Exception $e) {
            $this->_session = null;
        }
    }

    public function __get($name)
    {
        // Attributs with preg matching ^_.*Model are automagically
        // initialized.
        if (preg_match('/^_.*Model$/', $name)) {
            $className = ucfirst(
                preg_replace('/Model$/', '', preg_replace('/^_/', '', $name))
            );
            return new $className();
        } elseif ('_objDb' === $name) {
            $className = 'DbTable_' . get_class($this);
            return new $className();
        } elseif (preg_match('/^_.*Db$/', $name)) { // Attributs with
            // preg matching ^_.*Db are automagically inizilized.
            $className = 'DbTable_' . ucfirst(
                preg_replace('/Db$/', '', preg_replace('/^_/', '', $name))
            );
            return new $className();
        }
    }

}
