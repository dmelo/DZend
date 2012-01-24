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

    public function assertAjaxWorks($uri)
    {
        $this->request->setHeader('X-Requested-With', 'XMLHttpRequest');
        $this->dispatch($uri);
        $this->assertNotQuery('title');
    }

    public function assertAjax500($uri)
    {
        $this->assertAjaxWorks($uri);
        $this->assertResponseCode(500);
    }
}
