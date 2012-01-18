<?php

class DZend_Controller_Action extends Zend_Controller_Action
{
    protected $_session;
    protected $_request;
    protected $_loginRequired;

    public function init()
    {
        if($this->getRequest()->isXmlHttpRequest())
            $this->_helper->layout->disableLayout();

        $this->_session = DZend_Session_Namespace::get('session');
        $this->_request = $this->getRequest();

    }

    public function preDispatch()
    {
        if($this->_loginRequired && !isset($this->_session->user)) {
            echo $this->view->t('Permission denied');
            die;
        }
    }
}
