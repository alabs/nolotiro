<?php

/**
 * Zets user controller - Handling user related actions - currently log in and
 * log out.
 * 
 * @version $Id: UserController.php,v 1.3 2007-12-04 16:54:49 seva Exp $
 */

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Registry.php';
require_once 'Zend/Session.php';

class UserController extends Zend_Controller_Action
{
    /**
     * User session
     *
     * @var Zend_Session_Namespace
     */
    protected $session = null;

    /**
     * Overriding the init method to also load the session from the registry
     *
     */
    public function init()
    {
        parent::init();
        $this->session = Zend_Registry::get('session');
    }

    /**
     * Default action - if logged in, log out. If logged out, log in.
     *
     */
    public function indexAction()
    {
        if ($this->session->authenticated) {
            $this->_forward('logout');
        } else {
            $this->_forward('login');
        }
    }

    /**
     * Log in - show the login form or handle a login request
     * 
     * @todo Implement real authentication
     */
    public function loginAction()
    {
        if ($this->getRequest()->getMethod() != 'POST') {
            // Not a POST request, show log-in form
            $this->render();
        
        } else {
            // Handle log-in form
            $user = $this->getRequest()->getParam('user');
            $pass = $this->getRequest()->getParam('password');
            
            // TODO Fix here...
            if ($user == $pass) {
                $this->session->logged_in = true;
                $this->session->username = $user;
                
                Zend_Session::regenerateId();
                $this->_redirect('/');
                
            // Wrong user name / password
            } else {
                $view = $this->initView();
                $view->user = $user;
                $view->error = 'Wrong user name or password, please try again';
                
                $this->render();
            }
        }
    }

    /**
     * Log out - delete user information and clear the session, then redirect to
     * the log in page.
     */
    public function logoutAction()
    {
        $this->session->logged_in = false;
        $this->session->username = false;
        
        $this->_redirect('/user/login');
    }
    
    /**
     * register - register a new user into the nolotiro database
     */
    
 public function registerAction()
    {
       
        
        //$this->_redirect('/user/login');
    }
}
