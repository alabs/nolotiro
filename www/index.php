<?php
/**
 * Bootstrap file for nolotiro V2
 *
 * @author Daniel Remeseiro
 *
 * All the sourcecode of this software is under GNU Affero General Public License
 * @see LICENSE file on application directory
 * @license http://www.gnu.org/licenses/agpl.txt
 */


// Define path to application directory
defined('APPLICATION_PATH')
    || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/../application'));

// Define path to nolotiro main directory
defined('NOLOTIRO_PATH')
    || define('NOLOTIRO_PATH', realpath(dirname(__FILE__) . '/../'));

// Define application environment
defined('APPLICATION_ENV')
    || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : 'production'));

// Ensure library/ is on include_path
set_include_path(implode(PATH_SEPARATOR, array(
    realpath(APPLICATION_PATH . '/../library'),
    get_include_path(),
)));

/** Zend_Application */
require_once 'Zend/Application.php';

// Create application, bootstrap, and run
$application = new Zend_Application(
    APPLICATION_ENV,
    APPLICATION_PATH . '/config/nolotiro.ini'
);




$application->bootstrap()->run();

