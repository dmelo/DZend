<?php

class DZend_Chronometer
{
    private $_start;
    private $_stop;

    public function __construct()
    {
        $this->_start = null;
        $this->_stop = null;
    }

    public function start()
    {
        $this->_start = microtime(true);
    }

    public function stop()
    {
        $this->_stop = microtime(true);
    }

    public function get()
    {
        return $this->_stop - $this->_start;
    }
}
