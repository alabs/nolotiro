<?php
/**
 * @author dani remeseiro
 */


class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->setEncoding('UTF-8');
        $view->doctype('XHTML1_STRICT');

        //ZendX_JQuery::enableView($view);

    }

    protected function _initAutoload()
    {
        $moduleLoader = new Zend_Application_Module_Autoloader(array(
            'namespace' => '',
            'basePath' => APPLICATION_PATH));

        return $moduleLoader;
    }



    protected function _initPlugins()
    {


        Zend_Controller_Action_HelperBroker::addPath( APPLICATION_PATH .'/controllers/helpers');


        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin ( new Nolotiro_Controller_Plugin_Language() );


        //set the routers
        $router = $front->getRouter ();

        
        $routeWoeid = new Zend_Controller_Router_Route( ':language/woeid/:woeid/:ad_type/*', array( 'language' => $_COOKIE['lang'], 'controller' => 'ad', 'action' => 'list') );
        //setting the language route url (the default also)
        $routeLang = new Zend_Controller_Router_Route ( ':language/:controller/:action/*', array ( $_COOKIE['lang'], 'controller' => 'index', 'action' => 'index' ) );


        $router->addRoute ( 'default', $routeLang );//important, put the default route first!
        $router->addRoute ( 'woeid/woeid/ad_type', $routeWoeid );
        $front->setRouter ( $router );


         return $front;
    }


}

