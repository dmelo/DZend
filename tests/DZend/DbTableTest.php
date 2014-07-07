<?php

class DZend_DbTableTest extends PHPUnit_Framework_TestCase
{
    protected $_taModel;

    protected function _assertTaRow($row)
    {
        $this->assertInstanceOf('DbTable_TaRow', $row);
    }

    protected function _assertTaRowSet($rowSet)
    {
        $this->assertInstanceOf('Zend_Db_Table_RowSet', $rowSet);
        foreach ($rowSet as $row) {
            $this->_assertTaRow($row);
        }
    }

    public function setUp()
    {
        parent::setUp();
        $this->_taModel = new Ta();
        $this->_taModel->truncate();
        $this->_taModel->insert(array('name' => 'test1', 'group' => 'g1'));
        $this->_taModel->insert(array('name' => 'test2', 'group' => 'g1'));
        $this->_taModel->insert(array('name' => 'test3', 'group' => 'g2'));
    }

    public function testSanity()
    {
        $this->assertTrue(true);
    }

    public function testFindRowById()
    {
        $this->_assertTaRow($this->_taModel->findRowById(1));
    }

    public function testFindRowByColumnName()
    {
        $this->_assertTaRow($this->_taModel->findRowByName('test2'));
    }

    public function testFindByColumnName()
    {
        $this->_assertTaRowSet($this->_taModel->findByGroup('g1'));
    }
}
