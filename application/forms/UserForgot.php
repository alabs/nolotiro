<?php

/**
 * This is the UserLogin form.   
 */


class Form_UserForgot extends Zend_Form
{
    /**
     * @see    http://framework.zend.com/manual/en/zend.form.html
     * @return void
     */ 
    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');

        
        
        $this->addElement('text', 'email', array(
            'label'      => 'Your email:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            )
        ));
		

        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => 'Send',
        ));
    }
}
