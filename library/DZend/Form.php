<?php

class DZend_Form_Parent extends Twitter_Bootstrap_Form_Vertical
{
};

class DZend_Form extends DZend_Form_Parent
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
        if($this->_useBootstrap) {
            /*
            EasyBib_Form_Decorator::setFormDecorator(
                $this, EasyBib_Form_Decorator::BOOTSTRAP
            );
            */
        }
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
        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'label' => $this->_t($label),
        ));
    }

    public function addCancel($label)
    {
        $this->addElement('button', 'cancel', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_DANGER,
            'label' => $this->_t($label),
        ));
    }

    public function addSubmitAndCancel($submitLabel, $cancelLabel)
    {
        $this->addSubmit($submitLabel);
        $this->addCancel($cancelLabel);

        $this->addDisplayGroup(
            array('submit', 'cancel'),
            'actions',
            array(
                'disableLoadDefaultDecorators' => true,
                'decorators' => array('Actions')
            )
        );
    }


    /**
     * addPassword Add password field with placaholder and label.
     *
     * @return void
     */
    public function addPassword()
    {
        $element = new Zend_Form_Element_Password('password');
        $element->setRequired();
        $element->setAttrib('placeholder', "******");
        $element->setLabel($this->_t('Password'));
        $this->addElement($element);
    }

    /**
     * addConfirmPassword Add confirm password field with placeholder and
     * label.
     *
     * TODO: make it check if it's equal to password.
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
