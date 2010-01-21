<?php
/**
 * This is the New Message  form.
 */

class New_Message extends Zend_Form {
	
	public function init() {
		// set the method for the display form to POST
		$this->setMethod ( 'post' );
		
		
                $this->addElement ( 'text', 'subject', array ('label' => 'Subject:', //'filters' => array('StringTrim', 'StringToLower'),
		'validators' => array (array ('StringLength', false, array (10, 100 ) ) ), 'required' => true )

		 );
		
		$this->addElement ( 'textarea', 'message', array ('label' => 'Your message:', 'validators' => array (array ('StringLength', false, array (3, 2000 ) ) ), 'required' => true )

		 );
		
		
		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
