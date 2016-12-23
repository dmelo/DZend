<?php

class DZend_Controller_Action extends Zend_Controller_Action
{
    use DZend_CurrentUser;

    protected $_request;
    protected $_logger;
    protected $_loginRequired = false;
    protected $_jsonify = false;
    protected $_objListCache = array();

    public function init()
    {
        try {
            $domain = Zend_Registry::get('domain');
        } catch (Zend_Exception $e) {
            $domain = '';
        }

        if (
            $this->getRequest()->isXmlHttpRequest()
            || 'application/json' === $this->getRequest()->getHeader('content-type')
            || $this->getRequest()->getParam('ajax') == 1
        ) {
            $this->view->isAjax = true;
            $this->_helper->layout->disableLayout();
        } else {
            $this->view->isAjax = false;
            try {
                $domain = Zend_Registry::get('domain');
            } catch (Zend_Exception $e) {
                $domain = '';
            }

            $version = trim(file_get_contents('../version.txt'));
            $domainJs = $domain . '/js/';
            $domainCss = $domain . '/css/';

            $view = $this->view;

            $view->doctype('HTML5');

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
