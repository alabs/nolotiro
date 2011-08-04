<?php

class Zend_Controller_Action_Helper_Checklang extends Zend_Controller_Action_Helper_Abstract
{


    function init()
    {
        //first try to get from url
        $this->lang = $this->getRequest()->getParam('language');

        //if not from url try to get from authenticated user session
        if ($this->lang == null) {
            $auth = Zend_Auth::getInstance();
            if ($auth->hasIdentity()) $this->lang = $auth->getIdentity()->lang;
        }




        $locale = new Zend_Locale ($this->lang);
        if (!in_array($locale->getLanguage(), array('en', 'es'))) {
            //set by default spanish
            $locale->setLocale('es');
        }
        $this->lang = $locale->getLanguage();
        

        $options = array('scan' => Zend_Translate::LOCALE_FILENAME);
        $translate = new Zend_Translate ('csv', NOLOTIRO_PATH . '/application/languages/', 'auto', $options);

        if ($translate->isAvailable($this->lang)) {
            $translate->setLocale($locale);
            Zend_Form::setDefaultTranslator($translate);
            Zend_Registry::set('Zend_Translate', $translate);
        } else {
            header('Location: /es');
            exit;
        }
    }

    function check()
    {
        return $this->lang;
    }


}

