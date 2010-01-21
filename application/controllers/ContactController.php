<?php
/**
 * ContactController
 *  
 */

class ContactController extends Zend_Controller_Action {
	public function init() {
		$this->initView ();
		//$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->view->baseUrl = Zend_Controller_Front::getParam ( $route );


                $locale = Zend_Registry::get ( "Zend_Locale" );
		$this->lang = $locale->getLanguage ();

                $this->aNamespace = new Zend_Session_Namespace('Nolotiro');


		$this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		$this->view->mensajes = $this->_flashMessenger->getMessages ();

	}
	
	/**
	 * The default action - show the contact form
	 */
	
	public function indexAction() {
		$request = $this->getRequest ();
		$form = $this->_getContactForm ();

		
		// check to see if this action has been POST'ed to
		if ($this->getRequest ()->isPost ()) {
			
			// now check to see if the form submitted exists, and
			// if the values passed in are valid for this form
			if ($form->isValid ( $request->getPost () )) {
				
				// collect the data from the user
				$f = new Zend_Filter_StripTags ( );
				$email = $f->filter ( $this->_request->getPost ( 'email' ) );
				$message = $f->filter ( utf8_decode ( $this->_request->getPost ( 'message' ) ) );
				
				//get the username if its nolotiro user
				$user_info = $this->view->user->username;
				$user_info .= $_SERVER ['REMOTE_ADDR'];
				$user_info .= ' ' . $_SERVER ['HTTP_USER_AGENT'];
				$mail = new Zend_Mail ( );
				$mail->setBodyText ( $user_info . nl2br('\r\n'). $message );
				$mail->setFrom ( $email );
				$mail->addTo ( 'daniel.remeseiro@gmail.com', 'Daniel Remeseiro' );
				$mail->setSubject ( 'nolotiro.com - contact  from ' . $email );
				$mail->send ();
				
				$this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Message sent successfully!' ) );
				$this->_redirect ( '/'.$this->lang.'/ad/list/woeid/'.$this->aNamespace->location.'/ad_type/give' );
			
			}
		}
		// assign the form to the view
		$this->view->form = $form;
	
	}
	
	/**
	 *
	 * @return Form_Contact
	 */
	protected function _getContactForm() {
		require_once APPLICATION_PATH . '/forms/Contact.php';
		$form = new Form_Contact ( );
		//$form->setAction($this->_helper->url(''));
		return $form;
	}

}
