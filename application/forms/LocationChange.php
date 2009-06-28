<?php

/**
 * This is the main Contact form.   
 */

class Form_LocationChange extends Zend_Form {
	
	public function init() {
		// set the method for the display form to POST
		$this->setMethod ( 'post' );
		
		// add an email element
		$this->addElement ( 'text', 'location', array ('label' => 'Location:'
		, 'required' => true, 'filters' => array ('StringTrim' ) ) );
		
		
		
		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
