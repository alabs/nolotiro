<?php

/**
 * AdController
 * 
 * @author  dani remeseiro
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
			
		$this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		
		//$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseUrl = Zend_Controller_Front::getParam ( $route );
		
	
	
	
	}
	
	/**
	 * The default action - show a list where woeid and ad type 
	 * Get the woeid and the ad_type from the session reg
	 */
	public function listAction() {

	    
	    $woeid = $this->_request->getParam ( 'woeid' );
	    $ad_type = $this->_request->getParam ( 'ad_type' );
        
		$model = $this->_getModel ();
		$this->view->ad = $model->getAdList($woeid, $ad_type);
		
		
		//set the location reg var from the url
		$aNamespace = new Zend_Session_Namespace('Nolotiro');
        $aNamespace->location = $woeid;
        
        
        
        //paginator
                        
        
        $page=$this->_getParam('page');
        $paginator = Zend_Paginator::factory($this->view->ad);
        $paginator->setItemCountPerPage(2);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator=$paginator;

        
        
		
		///
		$this->view->mensajes = $this->_flashMessenger->getMessages ();
		$this->render ();

		
	}
	
	public function showAction() {
		
		
		$id = $this->_request->getParam ( 'id' );
		
		$model = $this->_getModel ();
		$this->view->ad = $model->getAd ( $id );
	
	}
	
	public function createAction() {
		
	    $locale = Zend_Registry::get ( "Zend_Locale" );
        $lang = $locale->getLanguage ();
	    
		//first we check if user is logged, if not redir to login
		$auth = Zend_Auth::getInstance ();
		if (! $auth->hasIdentity ()) {
			$this->_redirect ( $lang.'/auth/login' );
			
			
		} else {
			
			$request = $this->getRequest ();
			$form = $this->_getAdEditForm ();
			
			// check to see if this action has been POST'ed to
			if ($this->getRequest ()->isPost ()) {
				
				// now check to see if the form submitted exists, and
				// if the values passed in are valid for this form
				if ($form->isValid ( $request->getPost () )) {
					
					$formulario = $form->getValues ();
					
					//strip html tags to title
					$formulario['title'] = strip_tags($formulario['title']);
					
					//anti hoygan to title
					//dont use strtolower because dont convert utf8 properly . ej: á é ó ...
					$formulario['title'] = ucfirst(mb_convert_case($formulario['title'], MB_CASE_LOWER, "UTF-8")); 
					
					//strip html tags to body
					$formulario['body'] = strip_tags($formulario['body']);
					
					//anti hoygan to body
					$split=explode(". ", $formulario['body']);
                    
					foreach ($split as $sentence) {
                        $sentencegood = ucfirst(mb_convert_case($sentence, MB_CASE_LOWER, "UTF-8"));
                        $formulario['body'] = str_replace($sentence, $sentencegood, $formulario['body']);
                    }
					
                    
                    //get the ip of the ad publisher
                    if (getenv(HTTP_X_FORWARDED_FOR)) {							
                        $ip = getenv(HTTP_X_FORWARDED_FOR); 
                    } else { 
                        $ip = getenv(REMOTE_ADDR);
                    }
            
                    $formulario['ip'] = $ip;        
                    
					//get this ad user owner
					$formulario ['user_owner'] = $auth->getIdentity ()->id;
					
					//get date created
                    //TODO to use the Zend Date object to apapt the time to the locale user zone
					$datenow = date("Y-m-d H:i:s", time() );
					$formulario ['date_created'] = $datenow;
					
					//get woeid to assign to this ad
					//the location its stored at session location value 
                    //(setted by default on bootstrap to Madrid woeid number)
                    $aNamespace = new Zend_Session_Namespace('Nolotiro');
                    $formulario ['woeid_code'] = $aNamespace->location;
					
					
					$model = $this->_getModel ();
					$model->save ( $formulario );
					
					//Zend_Debug::dump ( $formulario );
                    $this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Ad published succesfully!' ) );
					$this->_redirect ( '/' );
					
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
	 * _getModel() is a protected utility method for this controller.
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
