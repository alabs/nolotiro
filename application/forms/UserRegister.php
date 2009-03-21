<?php

/**
 * This is the UserRegister form.   
 */


class Form_UserRegister extends Zend_Form
{
    /**
     * @see    http://framework.zend.com/manual/en/zend.form.html
     * @return void
     */ 
    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');

        // add an email element
        $this->addElement('text', 'email', array(
            'label'      => 'Your email:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            )
        ));

        $this->addElement('text', 'username', array(
        	'label'      => 'Choose a username:',
    		'filters' => array('StringTrim', 'StringToLower'),
			'validators' => array(
			array('StringLength', false, array(3, 20)),
			),
			'required' => true,
        	
		));

		$this->addElement('password', 'password', array(
		'filters' => array('StringTrim'),
		'validators' => array(
		array('StringLength', false, array(5, 20)),
		),
		'required' => true,
		'label' => 'Password:',
		));
		
		$this->addElement('captcha', 'captcha', array(
            'label'      => 'Please enter the 3 letters:',
            'required'   => true,
            'captcha'    => array('captcha' => 'Image',
								 'wordLen' => 3,
								 'height' => 50,
								 'width' => 120,
								 //'gcfreq'=> 100,
								 'font'=> NOLOTIRO_PATH_ROOT.'/www/images/antigonimed.ttf',
					 			 'imgdir'=>NOLOTIRO_PATH_ROOT.'/www/images/captcha')
        ));
		

        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => 'Register',
        ));
    }
}
