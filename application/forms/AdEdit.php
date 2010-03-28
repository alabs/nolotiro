<?php

/**
 * This is the AdEdit form.   
 */

class Form_AdEdit extends Zend_Form {
	
	public function init() {
		
		//set multipart to upload images
		$this->setAttrib('enctype', 'multipart/form-data');
		
		// set the method for the display form to POST
		$this->setMethod ( 'post' );

		//upload image stuff
		$this->addElement('file', 'photo', array(
			
			'label' => 'Select an image file for your ad (optional).',
			'required' => false,
			'setDestination' => (NOLOTIRO_PATH_ROOT. '/www/images/uploads'),
			'description' => 'Allowed format files: gif, jpg, png. Max:1Mb Size',
			'validators' => array(
			'Extension' => array(false, 'jpg,jpeg,bmp,gif,png'),
			'Size' => array('min' => 1, 'max' => 1000000),
			'IsImage' => array( 'image/bmp', 'image/gif', 'image/jpeg', 'image/pjpeg', 'image/jpg', 'image/png')
			)
			));

		
		$this->addElement ( 'select', 'type', array (

		'label' => 'Choose:', 'required' => true,
		 'attribs' => array ('type' => 'type', 'type' => 'type' ),
		 'multioptions' => array ('give' => 'i give...', 'want' => 'i want...' ) ) );
		
		$this->addElement ( 'text', 'title', array ('label' => 'Title of your ad:', //'filters' => array('StringTrim', 'StringToLower'),
		'validators' => array (array ('StringLength', false, array (10, 50 ) ) ), 'required' => true )

		 );
		$this->addElement ( 'textarea', 'body', array ('label' => 'Ad body:', 'validators' => array (array ('StringLength', false, array (30, 500 ) ) ), 'required' => true )

		 );
		
		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
