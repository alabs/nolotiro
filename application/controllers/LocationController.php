<?php
/**
 * LocationController
 * 
 */

class LocationController extends Zend_Controller_Action {
	
	public function init() {
		parent::init ();
		
		
	}

	
	
	
	
	/**
	 * get the location stored at session location value 
	 * (setted by default on bootstrap to Madrid woeid number)
	 * 
	 */
	
	public function indexAction(){
		//$this->_redirect ( '/' );
		
		 
	}

	
	
	
	public function changeAction(){
		$request = $this->getRequest();
		$form = $this->_getLocationChangeForm();
		
		// check to see if this action has been POST'ed to
		if ($this->getRequest ()->isPost ()) {
			
			
			if ($form->isValid ( $request->getPost () )) {
				
				
				$formulario = $form->getValues ();
				
				$aNamespace = new Zend_Session_Namespace('Nolotiro');
				$aNamespace->location = $formulario['location'];
				
				
				$this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Location changed successfully!' ) );
				//parent::render();
				//$this->_redirect ( '/' );
			
			}
		}
		// assign the form to the view
		$this->view->form = $form;
	}
	
	
	/**
	 *
	 * @return Form_LocationChange
	 */
	protected function _getLocationChangeForm() {
		require_once APPLICATION_PATH . '/forms/LocationChange.php';
		$form = new Form_LocationChange();
		//$form->setAction($this->_helper->url(''));
		return $form;
	}
	
}
?>

