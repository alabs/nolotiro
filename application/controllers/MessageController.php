<?php
/**
 * MessageController
 * this controller is to send and receive private messages
 */
class MessageController extends Zend_Controller_Action {

       public function init() {
		// Overriding the init method to also load the session from the registry
		parent::init ();
		$this->view->baseUrl = Zend_Controller_Front::getParam ( $route );

		$locale = Zend_Registry::get ( "Zend_Locale" );
		$this->lang = $locale->getLanguage ();

                ///
		$this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		$this->view->mensajes = $this->_flashMessenger->getMessages ();
	}


	public function indexAction() {
                //dont do nothing, just redir to /
                $this->_redirect ( '/' );
	}


        public function createAction(){

            $id_user_to = $this->_request->getParam ( 'id_user_to' );
            $form = $this->_getNewMessageForm();

            //first we check if user is logged, if not redir to login
		$auth = Zend_Auth::getInstance ();
		if (! $auth->hasIdentity ()) {

			//keep this url in zend session to redir after login
			$aNamespace = new Zend_Session_Namespace('Nolotiro');
			$aNamespace->redir = $this->lang.'/message/create/id_user_to/'.$id_user_to;

			//Zend_Debug::dump($aNamespace->redir);
			$this->_redirect ( $this->lang.'/auth/login' );


		} else {

            //the user is logged, then can create and send the private message

            

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
				$user_info .= ' ' . $_SERVER ['HTTP_USER_AGENT'] . '<br />';

				$mail = new Zend_Mail ( );
				$mail->setBodyText ( $user_info . $message );
				$mail->setFrom ( $email );
				$mail->addTo ( 'daniel.remeseiro@gmail.com', 'Daniel Remeseiro' );
				$mail->setSubject ( 'nolotiro.com - contact  from ' . $email );
				$mail->send ();

				$this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Private message sent successfully!' ) );
				$this->_redirect ( '/'.$this->lang.'/ad/list/woeid/'.$aNamespace->location.'/ad_type/give' );

			}
		}
		// assign the form to the view
		$this->view->form = $form;

                }


        }

        public function listAction(){

        }

        public function checkAction(){

        }





        /**
	 *
	 * @return New_Message
	 */
	protected function _getNewMessageForm() {
		require_once APPLICATION_PATH . '/forms/NewMessage.php';
		$form = new New_Message ( );
	
		return $form;
	}



}


