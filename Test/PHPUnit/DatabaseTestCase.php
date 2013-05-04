<?php

class DZend_Test_PHPUnit_DatabaseTestCase extends
    Zend_Test_PHPUnit_DatabaseTestCase
{
    private $_connectionMock;
    protected $_logger;

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
        $db->query("truncate table bond");
        $db->query(
            "update user set current_playlist_id = null"
        );
        $db->query("truncate table playlist");
        $db->query("truncate table user");
        $db->query("truncate table music_track_link");
        $db->query("truncate table task_parameter");
        $db->query("truncate table task_request");
        $db->query("truncate table task_set");
        $db->query("truncate table task_type");
        $db->query("SET FOREIGN_KEY_CHECKS=1");
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

    protected function setUp()
    {
        $this->_logger = Zend_Registry::get('logger');
        $this->setupDatabase();
        try {
        parent::setUp();
        } catch(Exception $e) {
            var_dump($e->getTrace());
        }
    }

    public function testSanity()
    {
        $this->assertTrue(true);
    }

    public static function assertDataSetsEqual($dsa, $dsb)
    {
        $dsFiltereda = new PHPUnit_Extensions_Database_DataSet_DataSetFilter(
            $dsa
        );
        return parent::assertDataSetsEqual($dsa, $dsb);
    }

    public function filterTable($tableName, $dataSet)
    {
        $filterDataSet = new PHPUnit_Extensions_Database_DataSet_DataSetFilter(
            $dataSet
        );
        if (is_string($tableName)) {
            $tableName = array($tableName);
        }

        foreach ($tableName as $name) {
            $filterDataSet->setExcludeColumnsForTable(
                $name, array('created', 'last_updated')
            );
        }

        return $filterDataSet;
    }
}
