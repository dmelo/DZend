<?php

class DZend_View_Helper_FormEmail extends Zend_View_Helper_FormText
{
    public function formEmail($name, $value = null, $attribs = null)
    {
        $xhtml = $this->formText($name, $value, $attribs);
        return str_replace("type=\"text\"", "type=\"email\"", $xhtml);
    }
}
