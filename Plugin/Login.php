<?php

class DZend_Plugin_Login extends Zend_Controller_Plugin_Abstract
{
    protected $_authAdapter;
    protected $_allowLogOutAccess;

    protected function _onAllowLogOutAccess($request)
    {
        $funcName = array(
            0 => 'getModuleName',
            1 => 'getControllerName',
            2 => 'getActionName'
        );

        foreach ($this->_allowLogOutAccess as $path) {
            $match = true;
            for ($i = 0; $i < count($path); $i++) {
                if ($path[$i] !== $request->{$funcName[$i]}()) {
                    $match = false;
                    break;
                }
            }

            if (true === $match) {
                return true;
            }
        }

        return false;
    }

    public function __construct()
    {
        $this->_allowLogOutAccess = array();
    }

    public function prepare()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV
        );
        $tableName = "user";
        $identityColumn = "email";
        $credentialColumn = "password";
        $credentialTreatment = "SHA1(?)";

        if (isset($config->dzend->plugin->login)) {
            $login = $config->dzend->plugin->login;
            foreach(array(
                'tableName',
                'identityColumn',
                'credentialColumn',
                'credentialTreatment'
            ) as $field)
                $$field = isset($login->{$field}) ? $login->{$field} : $$field;
        }

        $dbAdapter = Zend_Db_Table::getDefaultAdapter();

        $this->_authAdapter = new Zend_Auth_Adapter_DbTable(
            $dbAdapter,
            $tableName,
            $identityColumn,
            $credentialColumn,
            $credentialTreatment
        );

        Zend_Registry::set('authAdapter', $this->_authAdapter);
    }

    public function routeStartup(Zend_Controller_Request_Abstract $request)
    {
        $this->prepare();
    }

    public function routeShutdown(
        Zend_Controller_Request_Abstract $request
    )
    {
        $logger = Zend_Registry::get('logger');
        $auth = Zend_Auth::getInstance();
        $logger->debug(
            'Login -- hasIdentity ' . $auth->hasIdentity() . '. module: '
            . $request->getModuleName() . '. _onAllowLogOutAccess: '
            . $this->_onAllowLogOutAccess($request)
        );
        if (!$auth->hasIdentity() &&
            $request->getModuleName() !== 'Auth' &&
            !$this->_onAllowLogOutAccess($request)
        ) {
            $request->setModuleName("Auth")
                ->setControllerName("index")
                ->setActionName("login");
        } elseif ($auth->hasIdentity()) {
            $session = DZend_Session_Namespace::get('session');
            if (!isset($session->user)) {
                $userModel = new User();
                $session->user = $userModel->findByEmail($auth->getIdentity());
            }
            DZend_Session_Namespace::close();
        }
    }
}
