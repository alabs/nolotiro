<?php

/**
 * This is the UserLogin form.   
 */

class Form_UserLogin extends Zend_Form {
	/**
	 * @see    http://framework.zend.com/manual/en/zend.form.html
	 * @return void
	 */
	public function init() {
		// set the method for the display form to POST
		$this->setMethod ( 'post' );
		
		$this->addElement ( 'text', 'email', array ('label' => 'Your email:', 'filters' => array ('StringTrim', 'StringToLower' ),
		 'validators' => array ('EmailAddress' ), 'required' => true )

		 );
		
		$this->addElement ( 'password', 'password', array ('filters' => array ('StringTrim' ), 'validators' => array (array ('StringLength', false, array (5, 20 ) ) ), 'required' => true, 'label' => 'Password:' ) );

                $checkboxDecorator = array(
                                'ViewHelper',
                                'Errors',
                                array(array('data' => 'HtmlTag'), array('tag' => 'span', 'class' => 'element')),
                                array('Label', array('tag' => 'dt'),
                                array(array('row' => 'HtmlTag'), array('tag' => 'span')),
                            ));

                $this->addElement('checkbox', 'rememberme', array(
                    'decorators' => $checkboxDecorator,
                    'required' => true,
                    'checked' =>false
                    ));



		// add the submit button
		$this->addElement ( 'submit', 'submit', array ('label' => 'Login' ) );
	}
}
