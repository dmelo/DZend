<?php

define('__DIOGO_SESSION__', 'DZend_Session_Namespace_');

class DZend_Session_Namespace
{
    static public function get($namespace)
    {
        session_write_close();
        try {
            Zend_Session::start();
        } catch (Zend_Session_Exception $e) {
            session_start();
        }

        $key = __DIOGO_SESSION__ . $namespace;
        if (!is_array($_SESSION)) {
            $_SESSION = array();
        }

        if (!array_key_exists($key, $_SESSION)) {
            $obj = new stdClass();
            $_SESSION[$key] = $obj;
        }

        return $_SESSION[$key];
    }

    static public function close()
    {
        session_write_close();
    }

    static public function delete()
    {
        @session_start();
        session_destroy();
        session_write_close();
    }
}
