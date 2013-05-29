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

    /**
     * __construct Just initialize internals.
     *
     * @return void
     */
    public function __construct()
    {
        $this->_start = null;
        $this->_stop = null;
    }

    /**
     * start Start the chronometer.
     *
     * @return void
     */
    public function start()
    {
        $this->_start = microtime(true);
    }

    /**
     * stop Stop the chronometer.
     *
     * @return void
     */
    public function stop()
    {
        $this->_stop = microtime(true);
    }

    /**
     * get Get the timespam between the last time start and stop were called.
     *
     * @return float Return result in seconds.
     */
    public function get()
    {
        return $this->_stop - $this->_start;
    }
}
