<?php

/**
 * DZend_Chronometer Measure time of running code, meant for debuggin only.
 *
 * @package DZend
 * @version 1.0
 * @copyright Copyright (C) 2010 Diogo Oliveira de Melo. All rights reserved.
 * @author Diogo Oliveira de Melo <dmelo87@gmail.com>
 * @license GPL version 3
 */
class DZend_Chronometer
{
    private $_start;
    private $_stop;
    private $_ready;

    /**
     * __construct Just initialize internals.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_start = null;
        $this->_stop = null;
        $this->_ready = -1;
    }

    /**
     * start Start the chronometer.
     *
     * @return void
     */
    public function start()
    {
        $this->_start = microtime(true);
        $this->_ready = false;
    }

    /**
     * stop Stop the chronometer.
     *
     * @return void
     * @throws DZend_Chronometer_Exception
     */
    public function stop()
    {
        if (false !== $this->_ready) {
            throw new DZend_Chronometer_Exception(
                'start method was not called before'
            );
        }
        $this->_stop = microtime(true);
        $this->_ready = true;
    }

    /**
     * get Get the timespam between the last time start and stop were called.
     *
     * @return float Return result in seconds.
     * @throws DZend_Chronometer_Exception
     */
    public function get()
    {
        if (true === $this->_ready) {
            return $this->_stop - $this->_start;
        } else {
            throw new DZend_Chronometer_Exception(
                "Chronometer is not ready to return the time"
            );
        }
    }
}
