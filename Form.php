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

    /**
     * addEmail Add email field with placeholder, email validator, filters and
     * label.
     *
     * @return void
     */
    public function addEmail()
    {
        $element = new DZend_Form_Element_Email('email');
        $element->setRequired();
        $element->setAttrib('placeholder', $this->_t('john.smith@gmail.com'));
        $element->setLabel($this->_t('Email'));
        $element->addValidator('EmailAddress')
            ->addFilter('StringTrim')
            ->addFilter('StripTags');
        $this->addElement($element);
    }

    /**
     * addSubmit Add the submit field with label given by parameter
     *
     * @param string $label
     * @return void
     */
    public function addSubmit($label)
    {
        $element = new Zend_Form_Element_Submit('submit');
        $element->setLabel($label);
        $this->addElement($element);
    }

    /**
     * addPassword Add password field with placaholder and label.
     *
     * @return void
     */
    public function addPassword()
    {
        $element = new Zend_Form_Element_Password('password');
        $element->setAttrib('placeholder', "******");
        $element->setLabel($this->_t('Password'));
        $this->addElement($element);
    }

    /**
     * addConfirmPassword Add confirm password field with placeholder and
     * label.
     *
     * @return void
     */
    public function addConfirmPassword()
    {
        $element = new Zend_Form_Element_Password('password2');
        $element->setRequired();
        $element->setAttrib('placeholder', "******");
        $element->setLabel($this->_t('Confirm Password'));
        $this->addElement($element);
    }

    /**
     * addSimpleInput Simple text input element.
     *
     * @param string $name Name of the object.
     * @param string $label Label.
     * @param bool $required Wheter it's required for the form or not.
     * @return void
     */
    public function addSimpleInput($name, $label, $required = true)
    {
        $element = new Zend_Form_Element_Text(
            array(
                'name' => $name,
                'label' => $label,
                'required' => $required
            )
        );
        $this->addElement($element);
    }
}
