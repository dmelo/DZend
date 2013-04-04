<?php

class DZend_Validate_Name extends Zend_Validate_Abstract
{
    const INVALID = 'nameInvaild';

    protected $_messageTemplates = array(
        self::INVALID => 'Apenas letras são permitidas'
    );

    public function isValid($value)
    {
        if (!is_string($value)) {
            $this->_error(self::INVALID);
            return false;
        }

        $allowedChars = implode(range('a', 'z')) . implode(range('0', '9'))
            . implode(range('A', 'Z')) . 'áàãéêíóôúç&ÁÀÃÉÊÍÓÔÚ ';

        foreach (str_split($value) as $char) {
            if (strpos($allowedChars, $char) === false) {
                $this->_error(self::INVALID);
                return false;
            }
        }

        return true;
    }
}
