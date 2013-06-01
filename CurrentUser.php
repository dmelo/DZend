<?php

/**
 * Commonly used methods for users manipulated on session.
 *
 */
trait DZend_CurrentUser
{
    protected $_userRow = null;

    /**
     * _getUserRow Get current user database row
     *
     * @return void
     */
    protected function _getUserRow()
    {
        if (null === $this->_userRow) {
            $session = DZend_Session_Namespace::get('session');
            $this->_userRow = isset($session->user) ? $session->user : null;
            DZend_Session_Namespace::close();
        }

        return $this->_userRow;
    }

    /**
     * _getUserId Get current user id.
     *
     * @return void
     */
    protected function _getUserId()
    {
        return $this->_getUserRow()->id;
    }
}
