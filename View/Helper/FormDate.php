<?php

class DZend_View_Helper_FormDate extends Zend_View_Helper_FormText
{
    public function formDate($name, $value = null, $attribs = null)
    {
        $xhtml = $this->formText($name, $value, $attribs);
        return str_replace('type="text"', 'type="date"', $xhtml);
    }
}
