<?php

class DZend_View_Helper_A extends Zend_View_Helper_Abstract
{
    public function a($href, $innerHtml = null, array $attrs = array())
    {
        null === $innerHtml && $innerHtml = $href;
        $ret = '<a href="' . $href . '" ';
        foreach ($attrs as $key => $value) {
            $ret .= " $key=\"$value\"";
        }
        $ret .= '>' . $innerHtml . '</a>';

        return $ret;
    }
}
