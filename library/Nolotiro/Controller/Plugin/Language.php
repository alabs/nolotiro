<?php
/**
 * Front Controller Plugin
 *
 * @uses	   Zend_Controller_Plugin_Abstract
 * @subpackage Plugins
 */
class Nolotiro_Controller_Plugin_Language extends Zend_Controller_Plugin_Abstract
{
    public function dispatchLoopStartup(Zend_Controller_Request_Abstract $request)
    {
        $language = $request->getParam('language');
        switch($language)
        {
            case 'en':
                $locale=new Zend_Locale($language);
                break;
            default:
                $locale=new Zend_Locale('es');
        }
        $adapter = new Zend_Translate('csv',
                               NOLOTIRO_PATH_ROOT . '/application/languages/'.$locale.'.csv',
                                $locale);
        Zend_Registry::set('Zend_Translate', $adapter);
    }
}