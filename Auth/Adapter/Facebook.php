<?php

class DZend_Auth_Adapter_Facebook implements Zend_Auth_Adapter_Interface
{
    private $_identity;

    // TODO: implement request validation to check if the request was really
    // facebook's.
    public function authenticate()
    {
        return new Zend_Auth_Result(Zend_Auth_Result::SUCCESS, $this->_identity);
    }

    public function setIdentity($identity)
    {
        $this->_identity = $identity;
    }
}
