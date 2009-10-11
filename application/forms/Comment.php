<?php

/**
 * This is the ad Comment form.   
 */

class Form_Comment extends Zend_Form {
	
	public function init() {
		
		$this->setMethod ( 'post' );

		$this->addElement ( 'textarea', 'body', array ('label' => 'Your comment:', 'validators' => array (array ('StringLength', false, array (3, 2000 ) ) ), 'required' => true )

		 );
		
		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
