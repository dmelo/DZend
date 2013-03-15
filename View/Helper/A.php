<?php

class View_Helper_A extends Zend_View_Helper_Abstract
{
    public function a($href, $innerHtml)
    {
        return '<a href="' . $href . '">' . $innerHtml . '</a>';
    }
}
