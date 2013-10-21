<?php

class DZend_Auth_Adapter_Facebook implements Zend_Auth_Adapter_Interface
{
    private $_identity;
    private $_name;

    // TODO: implement request validation to check if the request was really
    // facebook's.
    public function authenticate()
    {
        $facebook = new Facebook(
            array(
                'appId' => Zend_Registry::get('facebookId'),
                'secret' => Zend_Registry::get('facebookSecret')
            )
        );
        $logger = Zend_Registry::get('logger');
        $logger->debug('facebook --------- ' . $facebook->getUser());
        try {
            $profile = $facebook->api('/me');
            $this->setIdentity($profile['email']);
            $this->setName($profile['name']);
            return new Zend_Auth_Result(
                Zend_Auth_Result::SUCCESS, $this->_identity
            );
        } catch (FacebookApiException $e) {
            $logger->err(
                'Error athenticating user on facebook '
                . $e->getMessage() . ' # ' . $e->getTraceAsString()
            );
            return new Zend_Auth_Result(Zend_Auth_Result::FAILURE, $this->_identity);
        }
    }

    public function setIdentity($identity)
    {
        $this->_identity = $identity;
    }

    public function setName($name)
    {
        $this->_name = $name;
    }


    public function getName()
    {
        return $this->_name;
    }
}
