<?php

class DZend_Test_PHPUnit_DatabaseTestCase extends
    Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;

    protected function getConnection()
    {
        $config = new Zend_Config_Ini(
            APPLICATION_PATH . '/configs/application.ini',
            'testing'
        );
        $connection = Zend_Db::factory(
            $config->resources->db->adapter,
            $config->resources->db->params
        );

        $this->_connectionMock = $this->createZendDbConnection(
            $connection, 'zfunittests'
        );
        Zend_Db_Table_Abstract::setDefaultAdapter($connection);

        return $this->_connectionMock;
    }

    protected function getDataSet()
    {
        return $this->createFlatXmlDataSet(
            APPLICATION_PATH . '/../tests/application/models/dataset.xml'
        );
    }

    protected function _preInit()
    {
        $db = $this->getAdapter();
        $db->query("SET FOREIGN_KEY_CHECKS=0");
        $db->query("truncate table playlist_has_track");
        $db->query("truncate table user_listen_playlist");
        $db->query("truncate table track");
        $db->query(
            "update user set current_playlist_id = null"
        );
        $db->query("truncate table playlist");
        $db->query("truncate table user");
        $db->query("SET FOREIGN_KEY_CHECKS=1");
    }

    protected function setUp()
    {
        $this->_preInit();
        $db = $this->getAdapter();
        $db->query("SET FOREIGN_KEY_CHECKS=0");
        parent::setUp();
        $db->query("SET FOREIGN_KEY_CHECKS=1");
    }

    protected function tearDown()
    {
        $this->_preInit();
        parent::tearDown();
    }

    public function testSanity()
    {
        $this->assertTrue(true);
    }
}
