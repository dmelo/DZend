<?php

class TModel extends DZend_Model
{
    public function getTModelObject()
    {
        return $this->_tModelModel;
    }

    public function getLogger()
    {
        return $this->_logger;
    }
}

