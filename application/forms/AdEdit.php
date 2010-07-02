<?php

/**
 * This is the AdEdit form.   
 */

class Form_AdEdit extends Zend_Form {
	
	public function init() {
		
		//set multipart to upload images
		$this->setAttrib('enctype', 'multipart/form-data');	
		$this->setMethod ( 'post' );


                $this->addElement ( 'select', 'type', array (
		'label' => 'Ad type:', 'required' => true,
		 'attribs' => array ('type' => 'type', 'type' => 'type' ),
		 'multioptions' => array ('give' => 'i give...', 'want' => 'i want...' ) ) );
		
		$this->addElement ( 'text', 'title', array ('label' => 'Title of your ad:', //'filters' => array('StringTrim', 'StringToLower'),
		'validators' => array (array ('StringLength', false, array (10, 50 ) ) ), 'required' => true )

		 );
		$this->addElement ( 'textarea', 'body', array ('label' => 'Ad body:', 'validators' => array (array ('StringLength', false, array (30, 500 ) ) ), 'required' => true )

		 );


                $checkboxDecorator = array(
                                'ViewHelper',
                                'Errors',
                                array(array('data' => 'HtmlTag'), array('tag' => 'span', 'class' => 'element')),
                                array('Label', array('tag' => 'dt'),
                                array(array('row' => 'HtmlTag'), array('tag' => 'span')),
                            ));

                $this->addElement('checkbox', 'comments_enabled', array(
                    'decorators' => $checkboxDecorator,
                    'required' => true,
                    'label' => 'Allow public comments',
                    'checked' =>true
                    ));


		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Send' ) );
	}
}
