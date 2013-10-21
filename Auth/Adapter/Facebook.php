<?php

class DZend_Auth_Adapter_Facebook implements Zend_Auth_Adapter_Interface
{
    private $_identity;

    // TODO: implement request validation to check if the request was really
    // facebook's.
    public function authenticate()
    {
        $facebook = new Facebook();
        $facebook->setAppId(Zend_Registry::get('facebookId'));
        $facebook->setAppSecret(Zend_Registry::get('facebookSecret'));
        $logger = Zend_Registry::get('logger');
        $logger->debug('facebook --------- ' . $facebook->getUser());
        try {
            $profile = $facebook->api('/me');
            return new Zend_Auth_Result(
                Zend_Auth_Result::SUCCESS, $profile['email']
            );
        } catch (FacebookApiException $e) {
            $logger->err(
                'Error athenticating user on facebook '
                . $e->getMessage() . ' # ' . $e->getStackAsString()
            );
            return new Zend_Auth_Result(Zend_Auth::FAILURE);
        }
    }

    public function setIdentity($identity)
    {
        $this->_identity = $identity;
    }
}
