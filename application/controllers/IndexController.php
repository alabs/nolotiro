<?php

/**
 * Nolotiro index controller - the default controller, showing the home page.
 * 
 * @version $Id: IndexController.php,v 1.3 2007-12-04 16:54:49 seva Exp $
 */


class IndexController extends Zend_Controller_Action
{

    /**
     * Override the init method to make sure no unauthorized users access
     * any action of this controller
     *
     */
    public function init()
    {
        parent::init();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->baseUrl = $this->_request->getBaseUrl();
        $this->view->user = Zend_Auth::getInstance()->getIdentity();
//        if (!Zend_Registry::get('session')->logged_in) {
//            $this->_redirect('/user/login');
//        }
    }

    public function indexAction()
    {
    	
    	
    	$this->view->mensajes = $this->_flashMessenger->getMessages();
        $this->render();
    }
    
    
    function preDispatch()
    {
    $auth = Zend_Auth::getInstance();
    if (!$auth->hasIdentity()) {
        //$this->_redirect('auth/login');
        
    }
}
    
    
}