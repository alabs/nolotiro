<?php

class Form_MessageReply extends Zend_Form {

    public function init() {

        $this->setMethod ( 'post' );

        $this->addElement ( 'textarea', 'body', array (
            'validators' => array (
                array ('StringLength', false, array (2, 800) )),
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
