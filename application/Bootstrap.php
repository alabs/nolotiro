<?php
/**
 * @author one atheist
 * @license Affero GPL License Version 3
 * @link http://www.gnu.org/licenses/agpl.html
 */


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initMetadataCache()
    {
        $cache = Zend_Cache::factory('Core', 'File',
                array('automatic_serialization' => true),
                array('cache_dir' => '/tmp'));
        Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
    }


    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

    }


    protected function _initTimeZone()
        {
            //TODO get the user time zone conditional from user data if logged
            date_default_timezone_set('Europe/Madrid');
        }


    protected function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH));

        return $moduleLoader;
    }



    protected function _initZFDebug()
    {

        if (APPLICATION_ENV!='production')
        {
            $autoloader = Zend_Loader_Autoloader::getInstance();
            $autoloader->registerNamespace('ZFDebug');

            $options = array(
                    'plugins' => array('Variables',
                            'File' => array('base_path' => APPLICATION_PATH),
                            'Memory',
                            'Time',
                            'Registry',
                            'Exception',
                            'Xhprof')
            );

            if ($this->hasPluginResource('db'))
            {
                $this->bootstrap('db');
                $db = $this->getPluginResource('db')->getDbAdapter();
                $options['plugins']['Database']['adapter'] = $db;
            }

            # Setup the cache plugin
            if ($this->hasPluginResource('cache'))
            {
                $this->bootstrap('cache');
                $cache = $this-getPluginResource('cache')->getDbAdapter();
                $options['plugins']['Cache']['backend'] = $cache->getBackend();
            }

            $debug = new ZFDebug_Controller_Plugin_Debug($options);

            $this->bootstrap('frontController');
            $frontController = $this->getResource('frontController');
            $frontController->registerPlugin($debug);
        }
    }





    protected function _initFront()
    {
        Zend_Controller_Action_HelperBroker::addPath( APPLICATION_PATH .'/controllers/helpers');
        $front = Zend_Controller_Front::getInstance();
        $router = $front->getRouter ();

         //set the language route url (the default also)
        $routeLang = new Zend_Controller_Router_Route (
            ':language/:controller/:action/*', array ('language' => null,
                                                      'controller' => 'index',
                                                      'action' => 'index'));
        $routeWoeid = new Zend_Controller_Router_Route (
            ':language/woeid/:woeid/:ad_type/*', array ( 'language' => null,
                                                         'controller' => 'ad',
                                                         'action' => 'list'));
        $routeProfile = new Zend_Controller_Router_Route (
            ':language/profile/:id', array ( 'language' => null,
                                             'controller' => 'user',
                                             'action' => 'profile'));
        $routeAd = new Zend_Controller_Router_Route (
            ':language/ad/:id/*', array ( 'language' => null,
                                          'controller' => 'ad',
                                          'action' => 'show'));
        $routeAdAll = new Zend_Controller_Router_Route (
            ':language/ad/listall/*', array ( 'language' => null,
                                              'controller' => 'ad',
                                              'action' => 'listall'));
        $routeAdListUSer = new Zend_Controller_Router_Route (
            ':language/ad/listuser/*', array ( 'language' => null,
                                               'controller' => 'ad',
                                               'action' => 'listuser'));
        $routeAdCreate = new Zend_Controller_Router_Route (
            ':language/ad/create/*', array ( 'language' => null,
                                             'controller' => 'ad',
                                             'action' => 'create'));
        $routeAdEdit = new Zend_Controller_Router_Route (
            ':language/ad/edit/*', array ( 'language' => null,
                                           'controller' => 'ad',
                                           'action' => 'edit'));
        $routeAdDelete = new Zend_Controller_Router_Route (
            ':language/ad/delete/*', array ( 'language' => null,
                                             'controller' => 'ad',
                                             'action' => 'delete'));
        $routeMessageReply = new Zend_Controller_Router_Route (
            ':language/message/reply/:id/*', array ( 'language' => null,
                                                     'controller' => 'message',
                                                     'action' => 'reply'));
        $routeMessageShow = new Zend_Controller_Router_Route (
            ':language/message/show/:id/*', array ( 'language' => null,
                                                    'controller' => 'message',
                                                    'action' => 'show'));
        $routeMessageList = new Zend_Controller_Router_Route (
            ':language/message/list/*', array ( 'language' => null,
                                                'controller' => 'message',
                                                'action' => 'list'));
        $routeMessageDelete = new Zend_Controller_Router_Route (
            ':language/message/delete/:id/*', array ( 'language' => null,
                                                      'controller' => 'message',
                                                      'action' => 'delete'));

        $router->addRoute ( 'default', $routeLang );//important, put the default route first!
        $router->addRoute ( 'woeid/woeid/ad_type', $routeWoeid );
        $router->addRoute ( 'profile/id', $routeProfile );
        $router->addRoute ( 'ad/id', $routeAd );
        $router->addRoute ( 'ad/listall', $routeAdAll );
        $router->addRoute ( 'ad/listuser', $routeAdListUSer);
        $router->addRoute ( 'ad/create', $routeAdCreate);
        $router->addRoute ( 'ad/edit', $routeAdEdit);
        $router->addRoute ( 'ad/delete', $routeAdDelete);
        $router->addRoute ( 'message/reply', $routeMessageReply);
        $router->addRoute ( 'message/show', $routeMessageShow);
        $router->addRoute ( 'message/list', $routeMessageList);
        $router->addRoute ( 'message/delete', $routeMessageDelete);

        $front->setRouter ( $router );
        return $front;
    }

}

