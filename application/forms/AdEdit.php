<?php

/**
 * This is the AdEdit form.   
 */


class Form_AdEdit extends Zend_Form
{

    public function init()
    {
        // set the method for the display form to POST
        $this->setMethod('post');

        
        $this->addElement('text', 'title', array(
        	'label'      => 'Choose a username:',
    		'filters' => array('StringTrim', 'StringToLower'),
			'validators' => array(
            'alnum',
            array('regex', false, array('/^[a-z]/i')),
        	array('StringLength', false, array(3, 20)),
			),
			'required' => true,
        	
		));
		$this->addElement('textarea', 'body', array(
        	'label'      => 'Ad body:',
			'validators' => array(
			array('StringLength', false, array(3, 500)),
			),
			'required' => true,
        	
		));
		

        // add the submit button
        $this->addElement('submit', 'submit', array(
            'label'    => 'Edit',
        ));
    }
}
