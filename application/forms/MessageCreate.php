<?php

class Form_MessageCreate extends Zend_Form {

    public function init() {

        $this->setMethod ( 'post' );

        $this->addElement ( 'text', 'subject', array (
            'label' => 'Subject:',
            'filters' => array('StringTrim', 'StringToLower'),
            'validators' => array (
                array ('StringLength', false, array (4, 50) )),
            'required' => true
        ));

        $this->addElement ( 'textarea', 'body', array (
            'validators' => array (
                array ('StringLength', false, array (3, 800) )),
            'rows' => 3,
            'required' => true
        ));
        $this->getElement('body')->removeDecorator('label');

        $this->addElement ( 'submit', 'submit', array (
            'label' => 'Send'
        ));
        $this->getElement('submit')->removeDecorator('DtDdWrapper');
  }
}
