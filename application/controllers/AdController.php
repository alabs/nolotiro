<?php

/**
 * AdController
 * 
 * @author
 * @abstract this is the Ad controller , 
 * do the crud relative to ads : create, show, edit, delete
 */

class AdController extends Zend_Controller_Action {
	
	/**
	 * Overriding the init method to also load the session from the registry
	 *
	 */
	public function init() {
		parent::init ();
		//$this->view->baseUrl = $this->_request->getBaseUrl();
		

		$this->view->baseUrl = Zend_Controller_Front::getParam ( $route );
	
	}
	
	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		$model = $this->_getModel ();
		$this->view->ads = $model->fetchAll ();
	
	}
	
	public function showAction() {
		
		
		$id = $this->_request->getParam ( 'id' );
		
		$model = $this->_getModel ();
		$this->view->ad = $model->getAd ( $id );
	
	}
	
	public function createAction() {
		
		//first we check if user is logged, if not redir to login
		$auth = Zend_Auth::getInstance ();
		if (! $auth->hasIdentity ()) {
			$this->_redirect ( '/es/auth/login' );
		} else {
			
			$request = $this->getRequest ();
			$form = $this->_getAdEditForm ();
			
			// check to see if this action has been POST'ed to
			if ($this->getRequest ()->isPost ()) {
				
				// now check to see if the form submitted exists, and
				// if the values passed in are valid for this form
				if ($form->isValid ( $request->getPost () )) {
					
					$formulario = $form->getValues ();
					
					$model = $this->_getModel ();
					
					$formulario ['user_owner'] = $auth->getIdentity ()->id;
					$model->save ( $formulario );
					
					Zend_Debug::dump ( $formulario );
				
				}
			}
		}
	
	}
	
	public function editAction() {
		$request = $this->getRequest ();
		$form = $this->_getAdEditForm ();
		
		// check to see if this action has been POST'ed to
		if ($this->getRequest ()->isPost ()) {
			
			// now check to see if the form submitted exists, and
			// if the values passed in are valid for this form
			if ($form->isValid ( $request->getPost () )) {
				
				// since we now know the form validated, we can now
				// start integrating that data submitted via the form
				// into our model
				$formulario = $form->getValues ();
				Zend_Debug::dump ( $formulario );
			
			}
		}
	}
	
	/**
	 *
	 * @return Form_AdEdit 
	 */
	protected function _getAdEditForm() {
		require_once APPLICATION_PATH . '/forms/AdEdit.php';
		$form = new Form_AdEdit ( );
		
		// assign the form to the view
		$this->view->form = $form;
		return $form;
	}
	
	public function deleteAction() {
	
	}
	
	/**
	 * _getModel() is a protected utility method for this controller. It is 
	 * responsible for creating the model object and returning it to the 
	 * calling action when needed. Depending on the depth and breadth of the 
	 * application, this may or may not be the best way of handling the loading 
	 * of models.
	 * 
	 * @return Model_User
	 */
	protected function _getModel() {
		if (null === $this->_model) {
			
			require_once APPLICATION_PATH . '/models/Ad.php';
			$this->_model = new Model_Ad ( );
		}
		return $this->_model;
	}

}
