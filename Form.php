<?php

class DZend_Form extends EasyBib_Form
{
    protected $_translate;
    protected $_useBootstrap;

    protected function _t($arg)
    {
        return $this->_translate->_($arg);
    }

    public function __construct($options = null)
    {
        $this->_useBootstrap = true;
        $this->_translate = Zend_Registry::get('translate');
        $this->setMethod('get');
        parent::__construct($options);
        if($this->_useBootstrap)
            EasyBib_Form_Decorator::setFormDecorator(
                $this, EasyBib_Form_Decorator::BOOTSTRAP, 'submit', 'cancel'
            );
    }

    public function addEmail()
    {
        $element = new Zend_Form_Element_Text('email');
        $element->setRequired();
        $element->setAttrib('type', 'email');
        $element->setAttrib('placeholder', $this->_t('john.smith@gmail.com'));
        $element->setLabel($this->_t('Email'));
        $element->addValidator('EmailAddress')
            ->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($element);
    }

    public function addSubmit($label)
    {
        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel($label);
        $this->addElement($element);
    }

    public function addPassword()
    {
        $element = new Zend_Form_Element_Password('password');
        $element->setRequired();
        $element->setAttrib('placeholder', "******");
        $element->setLabel($this->_t('Password'));
        $this->addElement($element);
    }

    public function addConfirmPassword()
    {
        $element = new Zend_Form_Element_Password('password2');
        $element->setRequired();
        $element->setAttrib('placeholder', "******");
        $element->setLabel($this->_t('Confirm Password'));
        $this->addElement($element);
    }
}
