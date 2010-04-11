<?php
/**
 * Front Controller Plugin
 *
 * @uses	   Zend_Controller_Plugin_Abstract
 * @subpackage Plugins
 */
class Nolotiro_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract {
	
	

	public function routeShutdown(Zend_Controller_Request_Abstract $request) {
		$locale = new Zend_Locale ( );
		$options = array ('scan' => Zend_Translate::LOCALE_FILENAME );
		$translate = new Zend_Translate ( 'csv', NOLOTIRO_PATH . '/application/languages/', 'auto', $options );
		$requestParams = $this->getRequest ()->getParams ();
		$language = (isset ( $requestParams ['language'] )) ? $requestParams ['language'] : false;
		if ($language == false) {
			$language = ($translate->isAvailable ( $locale->getLanguage () )) ? $locale->getLanguage () : 'es';
                        
		}
		if (! $translate->isAvailable ( $language )) {
			
			throw new Zend_Controller_Action_Exception ( 'This language is not available (yet)', 404 );
                        

		} else {


                        if(!$_COOKIE['lang']){
                               setcookie ( 'lang', 'en' , null, '/' );
                               $language = 'en';

                        }

                        $locale->setLocale ( $language );
                        $translate->setLocale ( $locale );
                        Zend_Form::setDefaultTranslator ( $translate );
                        setcookie ( 'lang', $locale->getLanguage (), null, '/' );
                        Zend_Registry::set ( 'Zend_Locale', $locale );
                        Zend_Registry::set ( 'Zend_Translate', $translate );



                }

	
	}

}