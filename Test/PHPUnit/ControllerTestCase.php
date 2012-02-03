<?php

class DZend_Test_PHPUnit_ControllerTestCase extends Zend_Test_PHPUnit_ControllerTestCase
{
    public function setUp()
    {
        $this->bootstrap = new Zend_Application(APPLICATION_ENV, APPLICATION_PATH . '/configs/application.ini');
        parent::setUp();
    }

    public function assertBasics($action, $controller = 'index', $module = 'default')
    {
        $this->assertAction($action);
        $this->assertController($controller);
        $this->assertModule($module);
    }

    public function setAjaxHeader()
    {
        $this->request->setHeader('X-Requested-With', 'XMLHttpRequest');
    }

    public function assertIsAjax($uri)
    {
        $this->setAjaxHeader();
        $this->dispatch($uri);
        $this->assertNotQuery('html head title');
    }

    public function assertAjaxWorks($uri)
    {
        $this->assertIsAjax($uri);
        $this->assertResponseCode(200);
    }

    public function assertAjax500($uri)
    {
        $this->assertIsAjax($uri);
        $this->assertResponseCode(500);
    }
}
