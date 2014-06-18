<?php

class DZend_View_Helper_AImg extends DZend_View_Helper_A
{
    public function aImg($href, $srcImg = '#', array $attr = array())
    {
        return $this->a($href, "<img src=\"$srcImg\"/>", $attr);
    }
}
