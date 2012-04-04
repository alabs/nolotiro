<?php

class Zend_Controller_Action_Helper_Checklang extends Zend_Controller_Action_Helper_Abstract
{
    static $langcodes = array('en'=>1, 'es'=>2, 'ca'=>3, 'gl'=>4, 'de'=>5, 'fr'=>6, 'pt'=>7, 'it'=>8);

        function init()
        {
            Zend_Registry::set('languages', array('en'=>'English', 'es'=>'Español', 'ca'=>'Català', 'gl'=>'Galego', 'de'=>'Deustch', 'fr'=>'Français', 'pt'=>'Português', 'it'=>'Italiano'));
            $activelangs = array('en'=>0, 'es'=>0, 'ca'=>1, 'gl'=>1, 'de'=>1, 'fr'=>1, 'pt'=>1, 'it'=>1,);
            Zend_Registry::set('activelangs', $activelangs);

            $this->lang = $this->getRequest()->getParam("language");
            if ($this->lang == null)
            {
                $auth = Zend_Auth::getInstance();
                if ($auth->hasIdentity()) $this->lang = $auth->getIdentity()->lang;
            }
            if ($this->lang == null && isset($_COOKIE['lang']))
                $this->lang = $_COOKIE['lang'];

            $locale = new Zend_Locale ($this->lang);

            if (!isset($activelangs[$locale->getLanguage()])) {
                $locale->setLocale ('en');
            }
            $this->lang = $locale->getLanguage();
            $this->langtest = $activelangs[$this->lang]==1;
            $options = array ('scan' => Zend_Translate::LOCALE_FILENAME );
            $translate = new Zend_Translate ( 'csv', APPLICATION_PATH . '/languages/', $this->lang, $options );

            if ($translate->isAvailable ( $this->lang )) {
                $translate->setLocale ( $locale );
                Zend_Form::setDefaultTranslator ( $translate );
                Zend_Registry::set ( 'Zend_Translate', $translate );
            } else {
                header('Location: /es');
                exit;
            }

            if ($this->langtest && (!isset($_COOKIE['langtest']) || $_COOKIE['langtest']!="0")) {
                $controller = $this->getActionController();

                if (isset($controller->view)) {
                    $controller->view->extra .= " advices";
                    if (!$controller->view->advices) $controller->view->advices = array();
                    $controller->view->advices["langtest"] = $controller->view->translate("BetaTranslation", "/{$this->lang}/page/translate?lang={$this->lang}");
                    $controller->view->headScript()->appendFile( '/js/jquery.advice.js');
                }
            }
        }

        function check(){
            return $this->lang;
        }

        function getcode($lang = null)
        {
            if (!isset($lang)) $lang = $this->lang;
            return Zend_Controller_Action_Helper_Checklang::$langcodes[$lang];
        }


}

