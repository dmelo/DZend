<?php

class ChronometerTest extends PHPUnit_Framework_TestCase
{
    public function testSuccess1()
    {
        $c = new DZend_Chronometer();
        $c->start();
        $c->stop();
        $a = $c->get();
        $this->assertTrue($a === (float) $a);
    }

    public function testSuccess2()
    {
        $c = new DZend_Chronometer();
        $c->start();
        $c->stop();
        $c->start();
        $c->stop();
        $a = $c->get();
        $this->assertTrue($a === (float) $a);
    }

    public function testSuccess3()
    {
        $c = new DZend_Chronometer();
        $c->start();
        $c->stop();
        $c->get();
        $c->get();
    }

    public function testError1()
    {
        $this->setExpectedException('DZend_Chronometer_Exception');
        $c = new DZend_Chronometer();
        $c->stop();
    }

    public function testError2()
    {
        $this->setExpectedException('DZend_Chronometer_Exception');
        $c = new DZend_Chronometer();
        $c->get();
    }

    public function testError3()
    {
        $this->setExpectedException('DZend_Chronometer_Exception');
        $c = new DZend_Chronometer();
        $c->start();
        $c->stop();
        $c->stop();
    }

    public function testError4()
    {
        $this->setExpectedException('DZend_Chronometer_Exception');
        $c = new DZend_Chronometer();
        $c->start();
        $c->stop();
        $c->start();
        $c->get();
    }
}
