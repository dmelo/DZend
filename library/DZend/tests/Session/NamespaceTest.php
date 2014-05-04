<?php

class Session_NamespaceTest extends PHPUnit_Framework_TestCase
{
    public function testSuccess1()
    {
        $test = DZend_Session_Namespace::get('test1');
        $test->t1 = true;
        DZend_Session_Namespace::close();
        $this->assertTrue(DZend_Session_Namespace::get('test1')->t1);
    }

    public function testSuccess2()
    {
        $aux = DZend_Session_Namespace::get('test2');
        $this->assertTrue($aux instanceof stdClass);
    }

    public function testSuccess3()
    {
        $aux = DZend_Session_Namespace::get('test3');
        $aux->t3 = 'string';
        DZend_Session_Namespace::delete();
        $aux = DZend_Session_Namespace::get('test3');
        $this->assertTrue(!isset($aux->t3));
    }

    public function testSuccess4()
    {
        $aux = DZend_Session_Namespace::get('test4');
        $aux->t4 = 'string';
        DZend_Session_Namespace::close();
        $aux->t5 = 'string';
        $aux2 = DZend_Session_Namespace::get('test4');

        $this->assertTrue(!isset($aux2->t5));
    }
}
