<?php

class SessionTest extends PHPUnit_Framework_TestCase
{
    public function testSession()
    {
        $a = DZend_Session_Namespace::get('n');
        $a->a = 10;
        $a->b = 20;
        DZend_Session_Namespace::close();

        $a = DZend_Session_Namespace::get('n');
        $this->assertEquals($a->a, 10);
        $this->assertEquals($a->b, 20);
    }
}
