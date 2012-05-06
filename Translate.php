<?php

class DZend_Translate extends Zend_Translate
{
    public function _() {
        $args = func_get_args();
        $num = func_num_args();

        $adapter = $this->getAdapter();
        $args[0] = $adapter->_($args[0]);

        if($num <= 1) {
            return $args[0];
        }

        return call_user_func_array('sprintf', $args);
    }
}
