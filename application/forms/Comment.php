<?php

/**
 * This is the main Contact form.   
 */

class Form_Comment extends Zend_Form {
	
	public function init() {
		// set the method for the display form to POST
		$this->setMethod ( 'post' );
		
		
		$this->addElement ( 'textarea', 'message', array ('label' => 'Your comment:', 'validators' => array (array ('StringLength', false, array (3, 2000 ) ) ), 'required' => true )

		 );
		
		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
