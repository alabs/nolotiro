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
		
		
		$this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );

		$this->view->user = Zend_Auth::getInstance ()->getIdentity ();

                $locale = Zend_Registry::get ( "Zend_Locale" );
		$this->lang = $locale->getLanguage ();
                $this->view->lang = $locale->getLanguage ();
		
	
	}
	
	public function indexAction() {
		
                //$request = $this->getRequest ();
                $this->view->lang =  $this->_helper->checklang->check();


		//redir to the list of ads of woeid + ad_type session stored , ad controller
		
		$aNamespace = new Zend_Session_Namespace('Nolotiro');
		$woeid = $aNamespace->location;
		$ad_type = $aNamespace->ad_type;

                if(!$woeid){
                    $woeid = 766273; // madrid, spain by default
                }

                
		$this->_redirect('/'.$this->view->lang.'/woeid/'.$woeid.'/give');
	}
	
	

}