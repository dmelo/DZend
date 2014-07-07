<?php

class DZend_ModelTest extends PHPUnit_Framework_TestCase
{
    public function testSanity()
    {
        $this->assertTrue(true);
    }

    public function testAutoInstantiateModel()
    {
        $tModel = new TModel();
        $this->assertTrue($tModel->getTModelObject() instanceof TModel);
        $this->assertTrue($tModel->getTModelObject() instanceof TModel);
    }

    public function testLogger()
    {
        $tModel = new TModel();
        $logger = $tModel->getLogger();
        $this->assertTrue($logger instanceof Zend_Log);
        $this->assertNull($logger->debug('Test log priority debug'));
        $this->assertNull($logger->err('Test log priority err'));
    }
}
