<?php

use Facebook\FacebookSession;
use Facebook\FacebookRequest;
use Facebook\GraphUser;
use Facebook\FacebookRequestException;
use Facebook\FacebookRedirectLoginHelper;

class DZend_Auth_Adapter_Facebook implements Zend_Auth_Adapter_Interface
{
    private $_identity;
    private $_name;

    public function authenticate()
    {
        $logger = Zend_Registry::get('logger');
        // Set credentials
        FacebookSession::setDefaultApplication(
            Zend_Registry::get('facebookId'),
            Zend_Registry::get('facebookSecret')
        );

        // Set callback URL
        $helper = new FacebookRedirectLoginHelper(
            Zend_Registry::get('domain') . '/Auth/index/login'
        );

        try {
            $session = $helper->getSessionFromRedirect();
        } catch (Exception $e) {
            $logger->err(
                "Could not get Facebook session."
                . $e->getMessage() . '#' .$e->getTraceAsString()
            );
        }

        if (isset($session)) {
            // User is logged in on facebook and have given the permission.
            $logger->debug('Facebook session acquired');
            try {
                $me = (new FacebookRequest(
                    $session, 'GET', '/me'
                ))->execute()->getGraphObject(GraphUser::className());
                $this->setIdentity($me->getEmail());
                $this->setName($me->getName());

                // Authentication successful
                return new Zend_Auth_Result(
                    Zend_Auth_Result::SUCCESS, $this->_identity
                );
            } catch (Exception $e) {
                // Some other error occurred
                $logger->err(
                    'Error authenticating user on facebook '
                    . $e->getMessage() . ' # ' . $e->getTraceAsString()
                );
                return new Zend_Auth_Result(
                    Zend_Auth_Result::FAILURE, $this->_identity
                );
            }
        } else {
            $url = $helper->getLoginUrl();
            $logger->debug("redirecting user to Facebook, for authentication: $url");
            header("Location: $url");
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
