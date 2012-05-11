<?php

class DZend_Plugin_Login extends Zend_Controller_Plugin_Abstract
{
    protected $_authAdapter;

    public function __construct()
    {
    }

    public function routeStartup()
    {
        $config = new Zend_Config_Ini(APPLICATION_PATH . "/configs/application.ini", APPLICATION_ENV);
        $tableName = "user";
        $identityColumn = "email";
        $credentialColumn = "password";
        $credentialTreatment = "SHA1(?)";

        if(isset($config->dzend->plugin->login)) {
            $login = $config->dzend->plugin->login;
            foreach(array('tableName', 'identityColumn', 'credentialColumn', 'credentialTreatment') as $field)
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

    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        if(!$auth->hasIdentity() && $request->getModuleName() !== 'Auth')
            $request->setModuleName("Auth")->setControllerName("index")->setActionName("login");
        else {
            $session = DZend_Session_Namespace::get('session');
            $userModel = new User();
            $session->user = $userModel->findByEmail($auth->getIdentity());
        }
    }
}
