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
        
//        $emailcheck = $_POST['email'];
//        $this->addElement('text', 'email2', array(
//            'label'      => 'Your email again:',
//            'required'   => true,
//            'filters'    => array('StringTrim'),
//            'validators' => array('Identical', $emailcheck)
//        ));
        
        $this->addElement('text', 'username', array(
        	'label'      => 'Choose a username:',
    		'filters' => array('StringTrim', 'StringToLower'),
			'validators' => array(
			array('StringLength', false, array(3, 20)),
			),
			'required' => true,
        	
		));

		$this->addElement('checkbox', 'terms', array(		
		'required' => true,
		'label' => 'acepta las putas condiciones!',
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
