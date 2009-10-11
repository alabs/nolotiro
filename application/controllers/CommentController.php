<?php

/**
 * CommentController
 * 
 * @author  dani remeseiro
 * @abstract this is the Ad Comment controller , 
 * do the crud relative to ad comment : create, show, edit, delete
 */

class CommentController extends Zend_Controller_Action {
	
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
	
	
	
	public function createAction() {
		
		
		
		$locale = Zend_Registry::get ( "Zend_Locale" );
		$lang = $locale->getLanguage ();
	    
		//first we check if user is logged, if not redir to login
		//$auth = Zend_Auth::getInstance ();
		//if (! $auth->hasIdentity ()) {
			//$this->_redirect ( $lang.'/auth/login' );	
			
		//} else {
			
			$request = $this->getRequest ();
			$ad_id = $this->_request->getParam ( 'ad_id' );
			$form = $this->_getCommentForm ();
			
			// check to see if this action has been POST'ed to
			if ($this->getRequest ()->isPost ()) {
				
				// now check to see if the form submitted exists, and
				// if the values passed in are valid for this form
				if ($form->isValid ( $request->getPost () )) {
					
					$formulario = $form->getValues ();
					
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
					$formulario['ads_id'] = $ad_id;
                    
					//get this ad user owner
					//$formulario ['user_owner'] = $auth->getIdentity ()->id;
					
					//get date created
					//TODO to use the Zend Date object to apapt the time to the locale user zone
					$datenow = date("Y-m-d H:i:s", time() );
					$formulario ['date_created'] = $datenow;
					
					
					$model = $this->_getModel ();
					$model->save( $formulario );
					
					Zend_Debug::dump ( $formulario );
					
					//TODO pass the message to parent
					//$mensajes = parent::getHelper('mensajes');
					$this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Comment published succesfully!' ) );
					$locale = Zend_Registry::get ( "Zend_Locale" );
					$lang = $locale->getLanguage ();

					$this->_redirect ( '/'.$lang.'/ad/show/id/'.$ad_id );
					
				}
			}
		}
	
	//}
	
	public function editAction() {
		$request = $this->getRequest ();
		$form = $this->_getCommentForm ();
		
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
	protected function _getCommentForm() {
		require_once APPLICATION_PATH . '/forms/Comment.php';
		$form = new Form_Comment ();
		
		// assign the form to the view
		$this->view->form = $form;
		return $form;
	}
	
	public function deleteAction() {
	
	}
	
	/**
	 * _getModel() is a protected utility method for this controller.
	 * 
	 * @return Model_Comment
	 */
	protected function _getModel() {
		if (null === $this->_model) {
			
			require_once APPLICATION_PATH . '/models/Comment.php';
			$this->_model = new Model_Comment ( );
		}
		return $this->_model;
	}
	
	

}
