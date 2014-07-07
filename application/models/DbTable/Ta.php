<?php

class DbTable_Ta extends DZend_Db_Table
{
    public function truncate()
    {
        $this->_db->query('truncate table ta');
    }
}
