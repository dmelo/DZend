<?php

trait DZend_CurrentUser
{
    protected $_userRow;

    protected function _getUserRow()
    {
        if (null === $this->_userRow) {
            $session = DZend_Session_Namespace::get('session');
            $this->_userRow = $session->user;
            DZend_Session_Namespace::close();
        }

        return $this->_userRow;
    }

    protected function _getUserId()
    {
        return $this->_getUserRow()->id;
    }
}
