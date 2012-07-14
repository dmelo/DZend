<?php

class DZend_Test_PHPUnit_ControllerTestCase extends
    Zend_Test_PHPUnit_ControllerTestCase
{
    protected $_databaseUsage = false;

    protected function _preInit($db)
    {
        $db->query("SET FOREIGN_KEY_CHECKS=0");
        $db->query("truncate table playlist_has_track");
        $db->query("truncate table user_listen_playlist");
        $db->query("truncate table music_track_link");
        $db->query("truncate table track");
        $db->query("update user set current_playlist_id = null");
        $db->query("truncate table playlist");
        $db->query("truncate table user");
        $db->query("SET FOREIGN_KEY_CHECKS=1");
    }

    public function setUp()
    {
        if($this->_databaseUsage)
            $this->setupDatabase();
        $this->bootstrap = new Zend_Application(
            'testing', APPLICATION_PATH . '/configs/application.ini'
        );
        parent::setUp();
    }

    public function setupDatabase()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            'testing'
        );
        $db = Zend_Db::factory(
            $config->resources->db->adapter,
            $config->resources->db->params
        );

        $connection = new Zend_Test_PHPUnit_Db_Connection(
            $db, 'database_schema_name'
        );
        $databaseTester = new Zend_Test_PHPUnit_Db_SimpleTester($connection);
        $databaseFixture =
            new PHPUnit_Extensions_Database_DataSet_FlatXmlDataSet(
                APPLICATION_PATH . '/../tests/application/models/dataset.xml'
            );

        $this->_preInit($db);

        $db->query("SET FOREIGN_KEY_CHECKS=0");
        $databaseTester->setupDatabase($databaseFixture);
        $db->query("SET FOREIGN_KEY_CHECKS=1");
    }



    public function assertBasics(
        $action, $controller = 'index', $module = 'default'
    )
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

    public function assertJsonMessage($message)
    {
        $resp = Zend_Json::decode($this->response->getBody());
        $this->assertEquals(
            $this->response->getBody(),
            Zend_Json::encode($message)
        );
    }
}
