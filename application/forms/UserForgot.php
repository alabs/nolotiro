<?php
/**
 * This is the UserForgot form.   
 */

class Form_UserForgot extends Zend_Form
{
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
