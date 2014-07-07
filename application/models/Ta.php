<?php

class Ta extends DZend_Model
{
    public function truncate()
    {
        $this->_objDb->truncate();
    }

    public function insert($data)
    {
        return $this->_objDb->insert($data);
    }

    public function findRowByName($name)
    {
        return $this->_objDb->findRowByName($name);
    }

    public function findByGroup($group)
    {
        return $this->_objDb->findByGroup($group);
    }

    public function insertMulti($data)
    {
        return $this->_objDb->insertMulti($data);
    }
}
