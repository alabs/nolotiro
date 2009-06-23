<?php

/**
 * This is the AdEdit form.   
 */

class Form_AdEdit extends Zend_Form {
	
	public function init() {
		// set the method for the display form to POST
		$this->setMethod ( 'post' );
		
		$this->addElement ( 'select', 'type', array (

		'label' => 'Choose:', 'required' => true, 'attribs' => array ('type' => 'type', 'type' => 'type' ), 'multioptions' => array ('give' => 'i give...', 'want' => 'i want...' ) ) );
		
		$this->addElement ( 'text', 'title', array ('label' => 'Title of your ad:', //'filters' => array('StringTrim', 'StringToLower'),
		'validators' => array (array ('StringLength', false, array (10, 50 ) ) ), 'required' => true )

		 );
		$this->addElement ( 'textarea', 'body', array ('label' => 'Ad body:', 'validators' => array (array ('StringLength', false, array (30, 500 ) ) ), 'required' => true )

		 );
		
		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
