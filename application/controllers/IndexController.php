<?php
/**
 * Nolotiro index controller - the default controller, showing the home page.
 * 
 */

class IndexController extends Zend_Controller_Action {
	
	
	public function init() {
		
		$this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		$this->view->user = Zend_Auth::getInstance ()->getIdentity ();
                $this->lang = $this->view->lang =  $this->_helper->checklang->check();


	}
	
	public function indexAction() {

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