<?php

class View_Helper_KeyValueTable extends Zend_View_Helper_Abstract
{
    public function keyValueTable($data, $full = true)
    {
        $ret = $full ?
            '<table class="table table-bordered table-striped"><tbody>' : '';
        foreach ($data as $key => $value) {
            $ret .= '<tr><td>' . $key . '</td><td>' . $value . '</td></tr>';
        }
        $ret .= $full ? '</tbody></table>' : '';

        return $ret;
    }
}
