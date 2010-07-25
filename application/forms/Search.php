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

        $this->removeElement('src');
        $this->removeDecorator('HtmlTag');
        $this->removeDecorator('DtDdWrapper');
        $this->removeDecorator('dd');


        // add the submit button
        $this->addElement ( 'submit', 'submit', array (
                'label' => 'Search',
                'class' => 'large magenta awesome') );
    }

}