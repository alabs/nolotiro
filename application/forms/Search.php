<?php

class Form_Search extends Zend_Form
{

    public function init()
    {
        $this->setMethod ( 'get' );
        $this->setAttrib("class", "searchbox");

        $this->addElement ( 'text', 'q', array (
                'required' => false,
                'value' => isset($_GET['q']) ? $_GET['q'] : null,
                'filters' => array ('StringTrim' , 'StripTags')
                ) );

        $this->removeDecorator('HtmlTag');
        $this->removeDecorator('DtDdWrapper');
        $this->removeDecorator('dd');

        $this->addElement( 'hidden' , 'ad_type', array(
            'required' => true,
            'value' => 1
        ));


        $this->addElement ( 'submit', 'submit', array ('label' => 'search' ) );

    }
}
