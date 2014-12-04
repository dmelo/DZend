<?php

class DZend_View_Helper_FormSearch extends Zend_View_Helper_FormText
{
    public function formSearch($name, $value = null, $attribs = null)
    {
        $xhtml = $this->formText($name, $value, $attribs);
        return str_replace("type=\"text\"", "type=\"search\"", $xhtml);
    }
}
