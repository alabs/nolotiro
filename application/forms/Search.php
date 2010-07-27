<?php

class Form_Search extends Zend_Form
{

    public function init()
    {
        global $content;

        $this->setMethod ( 'get' );
        $this->setAttrib("class", "searchbox");

        $this->addElement ( 'text', 'q', array (
                'required' => false,
                'filters' => array ('StringTrim' )
                ) );

        $this->removeDecorator('HtmlTag');
        $this->removeDecorator('DtDdWrapper');
        $this->removeDecorator('dd');

        $this->addElement ( 'submit', 'submit', array ('label' => 'search' ) );

    }
}