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
                $this->location = $this->_helper->checklocation->check();


	}
	
	public function indexAction() {

                
		$this->_redirect('/'.$this->view->lang.'/woeid/'.$this->location.'/give');
	}
	
	

}