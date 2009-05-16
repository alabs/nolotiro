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

        $this->addElement('text', 'email', array(
            'label'      => 'Your email:',
            'required'   => true,
            'filters'    => array('StringTrim'),
            'validators' => array(
                'EmailAddress',
            )
        ));
        
//        $this->addElement('password', 'password', array(
//                'filters' => array('StringTrim'),
//                'validators' => array(
//                array('StringLength', false, array(5, 20)),
//                ),
//                'required' => true,
//                'label' => 'Choose a password (5 or more characters):',
//                ));
		
        
        $this->addElement('text', 'username', array(
        	'label'      => 'Choose a username:',
    		'filters' => array('StringTrim', 'StringToLower'),
			'validators' => array(
            'alnum',
            array('regex', false, array('/^[a-z]/i')),
        	array('StringLength', false, array(3, 20)),
			),
			'required' => true,
        	
		));

		
		$this->addElement('captcha', 'captcha', array(
            'label'      => 'Please, insert the 4 characters shown:',
            'required'   => true,
            'captcha'    => array('captcha' => 'Image',
								 'wordLen' => 4,
								 'height' => 50,
								 'width' => 160,
								 'gcfreq'=> 50,
								 'timeout' => 300,
								 'font'=> NOLOTIRO_PATH_ROOT.'/www/images/antigonimed.ttf',
					 			 'imgdir'=>NOLOTIRO_PATH_ROOT.'/www/images/captcha')
        ));
		

        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => 'Register',
        ));
    }
}
