<?php

class DZend_View_Helper_FormEmail extends Zend_View_Helper_FormText
{
    public function formEmail($name, $value = null, $attribs = null)
    {
        $xhtml = $this->formText($name, $value, $attribs);
        if (strpos('class="', $xhtml) !== false) {
            $xhtml = str_replace('class="', 'class="form-control ', $xhtml);
            $xhtml = str_replace("type=\"text\"", "type=\"email\"", $xhtml);
        } else {
            $xhtml = str_replace("type=\"text\"", "type=\"email\" class=\"form-control\"", $xhtml);
        }

        return $xhtml;
    }
}
