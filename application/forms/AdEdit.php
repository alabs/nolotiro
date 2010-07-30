<?php
class Form_AdEdit extends Zend_Form {
	
	public function init() {
		
		//set multipart to upload images
		$this->setAttrib('enctype', 'multipart/form-data');	
		$this->setMethod ( 'post' );


                //upload photo
                $photonew = $this->createElement('file', 'photo');
                $photonew->setOrder(0);
                $photonew->setLabel('Select an image file for your ad (optional).');
                $photonew->setDescription( 'Allowed format files: gif, jpg, png. Max size:1Mb');
                $photonew->setRequired(false);
                $photonew->setDestination( '/tmp/');
                $photonew->setMaxFileSize(1048576);
                // ensure only 1 file
                $photonew->addValidator('Count', false, 1);

                $photonew->addValidator('Size',
                   array('min' => 100,
                         'max' => 1048576,
                         'bytestring' => true));
                $photonew->addValidator('ImageSize',
                   array('minwidth' => 10,
                         'minheight' => 10,
                         'maxwidth' => 900,
                         'maxheight' => 900));

                $photonew->addValidator('Extension', false, 'jpg,jpeg,png,gif'); // only JPEG, PNG and GIFs
                $photonew->addValidator('IsImage', false);
                $this->addElement($photonew);

                $this->addElement ( 'select', 'type', array (
		'label' => 'Ad type:', 'required' => true,
		 'attribs' => array ('type' => 'type', 'type' => 'type' ),
		 'multioptions' => array ('1' => 'i give...', '2' => 'i want...' ) ) );
		
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
		$this->addElement ( 'submit', 'submit', array ('label' => 'Save' ) );
	}
}
