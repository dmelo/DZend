<?php

class FormTest extends PHPUnit_Framework_TestCase
{
    protected function _getEmailForm()
    {
        $form = new DZend_Form();
        $form->addEmail();
        $this->assertTrue(
            $form->getElement('email') instanceof DZend_Form_Element_Email
        );

        return $form;
    }

    protected function _getPasswordForm()
    {
        $form = new DZend_Form();
        $form->addPassword();
        $this->assertTrue(
            $form->getElement('password') instanceof Zend_Form_Element_Password
        );

        return $form;
    }

    protected function _getConfirmPasswordForm()
    {
        $form = new DZend_Form();
        $form->addConfirmPassword();
        $this->assertTrue(
            $form->getElement('password2') instanceof Zend_Form_Element_Password
        );

        return $form;
    }


    protected function _getSimpleInputForm($required = true)
    {
        $form = new DZend_Form();
        $form->addSimpleInput('simple_input', 'Simple Input', $required);
        $this->assertTrue(
            $form->getElement('simple_input') instanceof Zend_Form_Element_Text
        );

        return $form;
    }

    public function testEmailSuccess1()
    {
        $data = array('email' => 'bla@gmail.com');
        $this->assertTrue($this->_getEmailForm()->isValid($data));
    }

    public function testEmailError1()
    {
        $data = array('email' => 'blaagmail.com');
        $this->assertTrue(!$this->_getEmailForm()->isValid($data));
    }

    public function testEmailError2()
    {
        $this->assertTrue(!$this->_getEmailForm()->isValid(array()));

    }

    public function testPasswordSuccess1()
    {
        $this->assertTrue($this->_getPasswordForm()
            ->isValid(array('password' => 'blabla')));
    }

    public function testPasswordError1()
    {
        $this->assertTrue(!$this->_getPasswordForm()->isValid(array()));
    }

    public function testConfirmPasswordSuccess1()
    {
        $this->assertTrue($this->_getConfirmPasswordForm()
            ->isValid(array('password2' => 'blabla')));
    }

    public function testConfirmPasswordError1()
    {
        $this->assertTrue(!$this->_getConfirmPasswordForm()->isValid(array()));
    }


    public function testSimpleInputSuccess1()
    {
        $this->assertTrue(
            $this->_getSimpleInputForm(true)->isValid(
                array('simple_input' => 'bla')
            )
        );
    }

    public function testSimpleInputError1()
    {
        $this->assertTrue(
            !$this->_getSimpleInputForm(true)->isValid(array())
        );
    }

    public function testSimpleInputSuccess2()
    {
        $this->assertTrue(
            $this->_getSimpleInputForm(false)->isValid(
                array('simple_input' => 'bla')
            )
        );
    }

    public function testSimpleInputSuccess3()
    {
        $this->assertTrue(
            $this->_getSimpleInputForm(false)->isValid(array())
        );
    }
}
