<?php

class DZend_Model
{
    use DZend_CurrentUser;

    protected $_logger;

    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
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
