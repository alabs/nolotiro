<?php

/**
 * This is the UserLogin form
 */

class Form_UserLogin extends Zend_Form {
    /**
     * @see    http://framework.zend.com/manual/en/zend.form.html
     * @return void
     */
    public function init() {

        $this->setMethod ( 'post' );

        $this->addElement ( 'text', 'email', array (
            'label' => 'Your email:',
            'filters' => array ('StringTrim', 'StringToLower'),
            'validators' => array ('EmailAddress'),
            'required' => true
        ));

        $this->addElement ( 'password', 'password', array (
            'label' => 'Password:',
            'filters' => array ('StringTrim'),
            'validators' => array (array ('StringLength', false, array (5,20))),
            'required' => true
        ));

        $this->addElement ( 'checkbox', 'rememberme', array (
            'label' => 'Remember me',
            'checked' =>false,
            'required' => true
        ));

        $this->addElement ( 'submit', 'submit', array ('label' => 'Login' ) );
  }
}
