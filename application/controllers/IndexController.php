<?php
/**
 * Nolotiro index controller - the default controller, showing the home page.
 * 
 */

class IndexController extends Zend_Controller_Action {
	
	/**
	 * Override the init method to make sure no unauthorized users access
	 * any action of this controller
	 *
	 */
	public function init() {
		parent::init ();
		
		$this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		
		//$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseUrl = Zend_Controller_Front::getParam ( $route );
		$this->view->user = Zend_Auth::getInstance ()->getIdentity ();
		
	
	}
	
	public function indexAction() {
		
		
		//redir to the list of ads of woeid + ad_type sessiob stored , ad controller
		
		$aNamespace = new Zend_Session_Namespace('Nolotiro');
		$woeid = $aNamespace->location;
		$ad_type = $aNamespace->ad_type;
	    
		
		
		$locale = Zend_Registry::get ( "Zend_Locale" );
		$lang = $locale->getLanguage ();
        
		$this->_redirect('/'.$lang.'/ad/list/woeid/'.$woeid.'/ad_type/'.$ad_type);
	}
	
	

}