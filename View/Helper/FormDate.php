<?php

class DZend_View_Helper_FormDate extends Zend_View_Helper_FormText
{
    public function formDate($name, $value = null, $attribs = null)
    {
        $xhtml = $this->formText($name, $value, $attribs);
        return '<div class="input-append date" data-date="12/02/2012" '
            . 'data-date-format="dd/mm/yyyy">' . $xhtml
            . '<span class="add-on"><i class="icon-calendar"></i></span></div>';
    }
}
