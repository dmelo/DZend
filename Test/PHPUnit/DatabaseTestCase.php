<?php

class DZend_Test_PHPUnit_DatabaseTestCase extends Zend_Test_PHPUnit_DatabaseTestCase
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
        $this->getAdapter()->query("truncate table playlist_has_track");
        $this->getAdapter()->query("truncate table user_listen_playlist");
        $this->getAdapter()->query("truncate table track");
        $this->getAdapter()->query("update user set current_playlist_id = null");
        $this->getAdapter()->query("truncate table playlist");
        $this->getAdapter()->query("truncate table user");
    }

    protected function setUp()
    {
        $this->_preInit();
        parent::setUp();
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
