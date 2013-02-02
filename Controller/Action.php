<?php

class DZend_Controller_Action extends Zend_Controller_Action
{
    protected $_session;
    protected $_request;
    protected $_logger;
    protected $_loginRequired = false;
    protected $_jsonify = false;

    public function init()
    {
        if (
            $this->getRequest()->isXmlHttpRequest()
            || $this->getRequest()->getParam('ajax') == 1
        ) {
            $this->_helper->layout->disableLayout();
        }

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

    public function __get($name)
    {
        // Attributs with preg matching ^_.*Model are automagically
        // initialized.
        if (preg_match('/^_.*Model$/', $name)) {
            $className = ucfirst(
                preg_replace('/Model$/', '', preg_replace('/^_/', '', $name))
            );
            return new $className();
        }
    }

    /**
     * postDispatch Make it easier to output Json.
     *
     * @return void
     *
     */
    public function postDispatch()
    {
        if (isset( $this->view->output ) && $this->_jsonify) {
            echo Zend_Json::encode($this->view->output);
        }
    }

}
