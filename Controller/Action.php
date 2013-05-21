<?php

class DZend_Controller_Action extends Zend_Controller_Action
{
    use DZend_CurrentUser;

    protected $_request;
    protected $_logger;
    protected $_loginRequired = false;
    protected $_jsonify = false;

    public function init()
    {
        try {
            $domain = Zend_Registry::get('domain');
        } catch (Zend_Exception $e) {
            $domain = '';
        }

        if (
            $this->getRequest()->isXmlHttpRequest()
            || $this->getRequest()->getParam('ajax') == 1
        ) {
            $this->_helper->layout->disableLayout();
        } else {
            try {
                $domain = Zend_Registry::get('domain');
            } catch (Zend_Exception $e) {
                $domain = '';
            }

            $version = file_get_contents('../version.txt');
            $domainJs = $domain . '/js/';
            $domainCss = $domain . '/css/';

            $view = $this->view;

            $view->doctype('HTML5');
            $view->headTitle('AMUZI - Online music player');


            try {
                $js = Zend_Registry::get('js');
                foreach ($js as $item) {
                    $view->lightningPackerScript()->appendFile(
                        "$domainJs/$item?v=$version"
                    );
                }
            } catch (Zend_Exception $e) {
            }

            try {
                $css = Zend_Registry::get('css');
                foreach ($css as $item) {
                    $view->lightningPackerLink()->appendStylesheet(
                        "$domainCss/$item?v=$version"
                    );
                }
            } catch (Zend_Exception $e) {
            }
        }


        $this->_request = $this->getRequest();
        try {
            $this->_logger = Zend_Registry::get('logger');
        } catch (Zend_Exception $e) {
        }
    }

    public function preDispatch()
    {
        if ($this->_loginRequired && null !== $this->_getUserRow()) {
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
