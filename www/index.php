<?php
/**
 * Bootstrap file for nolotiro V2
 *
 * @copyright Daniel Remeseiro
 * 
 * All the sourcecode of this software is under GNU GPL3 License
 * @see LICENSE file on application directory
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

//Setting paths
define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application/'));
set_include_path(APPLICATION_PATH . '/../library' . PATH_SEPARATOR . get_include_path());

// Set the nolotiro main root path
define('NOLOTIRO_PATH_ROOT', realpath(dirname(__FILE__) . '/../'));
set_include_path(NOLOTIRO_PATH_ROOT . PATH_SEPARATOR . get_include_path());


//Enabling the autoload classes
require_once "Zend/Loader.php";
Zend_Loader::registerAutoload();


//date_default_timezone_set('Europe/Madrid');



// Load Configuration
$config = new Zend_Config_Ini(NOLOTIRO_PATH_ROOT . '/config/nolotiro.ini', 'dev');
Zend_Registry::set('config', $config);

// Start Session
$session = new Zend_Session_Namespace('Nolotiro');
Zend_Registry::set('session', $session);




//Setup the ddbb
$dbAdapter = Zend_Db::factory($config->database);
Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

//Setup the registry
$registry = Zend_Registry::getInstance();
$registry->configuration = $config;
$registry->dbAdapter     = $dbAdapter;

unset($dbAdapter, $registry, $config);


// Set up the front controller and dispatch
try {
	$front = Zend_Controller_Front::getInstance();
	$front->throwExceptions(true);
    
	$front->setControllerDirectory(NOLOTIRO_PATH_ROOT . '/application/controllers');
    //baseurl useful for fixed paths: css, images, etc
	$front->setBaseUrl($config->www->baseurl);
    
	//load the language plugin
	
	$front->registerPlugin(new Nolotiro_Controller_Plugin_Language());
    
    //setting the language route url
    $route = new Zend_Controller_Router_Route(
			':language/:controller/:action/*',
				array(
					'language'   => $language,
					'module'	 => 'default',
					'controller' => 'index',
					'action'	 => 'index'
				)
			);

    $router = $front->getRouter();
    $router->addRoute('default', $route);
    $router->setGlobalParam('language', $language);
    $front->setRouter($router);
	
    
    $front->dispatch();

// Handle controller exceptions (usually 404)
} catch (Zend_Controller_Exception $e) {
	include 'errors/404.phtml';

// Handle all other exceptions
} catch (Exception $e) {
	include 'errors/500.phtml';

}