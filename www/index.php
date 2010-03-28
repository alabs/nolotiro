<?php
/**
 * Bootstrap file for nolotiro V2
 *
 * @copyright Daniel Remeseiro
 *
 * All the sourcecode of this software is under GNU Affero General Public License
 * @see LICENSE file on application directory
 * @license http://www.gnu.org/licenses/agpl.txt
 */

//Setting paths
define ( 'APPLICATION_PATH', realpath ( dirname ( __FILE__ ) . '/../application/' ) );
set_include_path ( APPLICATION_PATH . '/../library' . PATH_SEPARATOR . get_include_path () );

// Set the nolotiro main root path
define ( 'NOLOTIRO_PATH_ROOT', realpath ( dirname ( __FILE__ ) . '/../' ) );
set_include_path ( NOLOTIRO_PATH_ROOT . PATH_SEPARATOR . get_include_path () );

//1.8 autoloader way
require_once 'Zend/Loader/Autoloader.php';
$autoloader = Zend_Loader_Autoloader::getInstance ();
$autoloader->registerNamespace ( array ('Zend_', 'Nolotiro_' ) );

//date_default_timezone_set('Europe/Madrid');


// Load Configuration
$config = new Zend_Config_Ini ( NOLOTIRO_PATH_ROOT . '/config/nolotiro.ini', 'dev' );
Zend_Registry::set ( 'config', $config );

//Setup the ddbb
$dbAdapter = Zend_Db::factory ( $config->database );
Zend_Db_Table_Abstract::setDefaultAdapter ( $dbAdapter );



 Zend_Controller_Action_HelperBroker::addPath(
        APPLICATION_PATH .'/controllers/helpers');


//Setup the registry
$registry = Zend_Registry::getInstance ();
$registry->configuration = $config;
$registry->dbAdapter = $dbAdapter;


// setup application authentication
$auth = Zend_Auth::getInstance();
$auth->setStorage(new Zend_Auth_Storage_Session());


////if user is locked kill the session
// TODO move this piece of shit-code to the user controller when the admin user locked action will be created
if ($auth->hasIdentity()){
    $files = new Zend_Db_Table('users');
    $id_lamer = $auth->getIdentity()->id;
    $query = "SELECT locked FROM users WHERE id = ".$id_lamer;
    $result = $files->getAdapter()->query($query)->fetch();

    if ($result['locked'] == 1) {
       Zend_Session::destroy();
       echo 'your nolotiro.org account has been locked';
       die ();
    }

}




// Start Session
//Zend_Session::rememberMe(86400);

$session = new Zend_Session_Namespace ( 'Nolotiro' );
//$session->setExpirationSeconds(86400); //TODO now is one day, set one week if the option "remember me" on login form is checked



        $aNamespace->location = $woeid;
        //set the default values to show if the session is empty
        // Start Session is in the bootstrap

        if (!isset($session->location) || ($session->location == null)) {
            // if location is not setted , set the Madrid woeid
           $session->location = 766273;
           setcookie ( 'location', 766273, null, '/' );
        }



        if (!isset($session->ad_type)) {
            // if ad_type is not setted , set the 'give' status to show the ads on home
           $session->ad_type = 'give';
        }

Zend_Registry::set ( 'session', $session );
Zend_Session::start();

//$config = array(
//    'name' => 'session',
//    'primary' => 'id',
//    'modifiedColumn' => 'modified',
//    'dataColumn' => 'data',
//    'lifetimeColumn' => 'lifetime'
//);
//
//Zend_Session::setSaveHandler(new
//                Zend_Session_SaveHandler_DbTable($config));
//Zend_Session::start();


unset ( $dbAdapter, $registry, $config, $session, $auth );

// Set up the front controller and dispatch
try {
	$front = Zend_Controller_Front::getInstance ();
	$front->throwExceptions ( true );

	$front->setControllerDirectory ( NOLOTIRO_PATH_ROOT . '/application/controllers' );


	//load the language plugin
	$front->registerPlugin ( new Nolotiro_Controller_Plugin_Language ( ) );

	//setting the language route url
	$route = new Zend_Controller_Router_Route ( ':language/:controller/:action/*', array ('language' => 'es', 'module' => 'default', 'controller' => 'index', 'action' => 'index' ) );

	$router = $front->getRouter ();
	// Remove any default routes
	$router->removeDefaultRoutes ();
	$router->addRoute ( 'default', $route );

	$front->setRouter ( $router );


	$front->dispatch ();

// Handle controller exceptions (usually 404)
} catch ( Zend_Controller_Exception $e ) {
	include 'errors/404.phtml';

// Handle all other exceptions
} catch ( Exception $e ) {
	include 'errors/500.phtml';

}