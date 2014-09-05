<?php

class DZend_Model
{
    use DZend_CurrentUser;

    protected $_logger;
    protected $_objListCache = array();

    public function __construct()
    {
        $this->_logger = Zend_Registry::get('logger');
    }

    public function __get($name)
    {
        // Attributs with preg matching ^_.*Model are automagically
        // initialized.
        if (array_key_exists($name, $this->_objListCache)) {
            return $this->_objListCache[$name];
        } elseif (preg_match('/^_.*Model$/', $name)) {
            $className = ucfirst(
                preg_replace('/Model$/', '', preg_replace('/^_/', '', $name))
            );
            return $this->_objListCache[$name] = new $className();
        } elseif ('_objDb' === $name) {
            // Attribute _objDb refers to it's own DB object.
            $className = 'DbTable_' . get_class($this);
            return $this->_objListCache[$name] = new $className();
        } elseif (preg_match('/^_.*Db$/', $name)) { // Attributs with
            // preg matching ^_.*Db are automagically inizilized.
            $className = 'DbTable_' . ucfirst(
                preg_replace('/Db$/', '', preg_replace('/^_/', '', $name))
            );
            return $this->_objListCache[$name] = new $className();
        }
    }

    public function findAll()
    {
        return $this->_objDb->fetchAll();
    }

    public function findRowById($id)
    {
        return $this->_objDb->findRowById($id);
    }

    public function getMaxId()
    {
        return $this->_objDb->getMaxId();
    }
}
