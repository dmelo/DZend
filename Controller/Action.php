<?php

class DZend_Controller_Action extends Zend_Controller_Action
{
    protected $_session;
    protected $_request;
    protected $_logger;
    protected $_loginRequired = false;

    public function init()
    {
        if ($this->getRequest()->isXmlHttpRequest())
            $this->_helper->layout->disableLayout();

        $this->_session = DZend_Session_Namespace::get('session');
        $this->_request = $this->getRequest();
        $this->_logger = Zend_Registry::get('logger');
    }

    public function preDispatch()
    {
        if ($this->_loginRequired && !isset($this->_session->user)) {
            $this->getResponse()->setHttpResponseCode(500);
            $this->_helper->layout->disableLayout();
            $this->_forward('error', 'index');
        }
    }
}
