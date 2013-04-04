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

        $allowedChars = array_merge(
            range('A', 'Z'),
            range('0', '9'),
            range('a', 'z'),
            str_split('áàãéêíóôúç&')
        );

        foreach (str_split($value) as $char) {
            if (!in_array($char, $allowedChars)) {
                $this->_error(self::INVALID);
                return false;
            }
        }

        return true;
    }
}
