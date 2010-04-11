<?php

/**
 * This is the main Contact form.   
 */

class Form_Contact extends Zend_Form {
	
	public function init() {
		// set the method for the display form to POST
		$this->setMethod ( 'post' );
		
		// add an email element
		$this->addElement ( 'text', 'email', array ('label' => 'Your email:', 'required' => true, 'filters' => array ('StringTrim' ), 'validators' => array ('EmailAddress' ) ) );
		
		$this->addElement ( 'textarea', 'message', array ('label' => 'Your message:', 'validators' => array (array ('StringLength', false, array (20, 2000 ) ) ), 'required' => true )

		 );
		
		$this->addElement ( 'captcha', 'captcha', array ('label' => 'Please, insert the 4 characters shown:',
                    'required' => true, 'captcha' => array ('captcha' => 'Image', 'wordLen' => 4,
                        'height' => 50, 'width' => 160, 'gcfreq' => 50, 'timeout' => 300, 'font' => NOLOTIRO_PATH . '/www/images/antigonimed.ttf',
                        'imgdir' => NOLOTIRO_PATH . '/www/images/captcha' ) ) );
		
		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
