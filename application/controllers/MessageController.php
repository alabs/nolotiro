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

                 $this->aNamespace = new Zend_Session_Namespace('Nolotiro');

		$this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
		$this->view->mensajes = $this->_flashMessenger->getMessages ();
	}


	public function indexAction() {
                //dont do nothing, just redir to /
                $this->_redirect ( '/' );
	}


        public function createAction(){
           $request = $this->getRequest ();
           $id_user_to = $this->_request->getParam ( 'id_user_to' );

		$form = $this->_getNewMessageForm ();


                //first we check if user is logged, if not redir to login
                $auth = Zend_Auth::getInstance ();
                if (! $auth->hasIdentity ()) {

                        //keep this url in zend session to redir after login
                        $aNamespace = new Zend_Session_Namespace('Nolotiro');
                        $aNamespace->redir = $this->lang.'/message/create/id_user_to/'.$id_user_to;

                        //Zend_Debug::dump($aNamespace->redir);
                        $this->_redirect ( $this->lang.'/auth/login' );


                }

		// check to see if this action has been POST'ed to
		if ($this->getRequest ()->isPost ()) {

			// now check to see if the form submitted exists, and
			// if the values passed in are valid for this form
			if ($form->isValid ( $request->getPost () )) {

				// collect the data from the user
				$f = new Zend_Filter_StripTags ( );
				$email = $f->filter ( $this->_request->getPost ( 'email' ) ); //TODO get the email  sender user  from ddbb

                                $subject = $f->filter ( $this->_request->getPost ( 'subject' ) );
				$message = $f->filter ( utf8_decode ( $this->_request->getPost ( 'message' ) ) );

				//get the username if its nolotiro user
				$user_info = $this->view->user->username;
				$user_info .= $_SERVER ['REMOTE_ADDR'];
				$user_info .= ' ' . $_SERVER ['HTTP_USER_AGENT'];
				$mail = new Zend_Mail ( );
				$mail->setBodyText ( $user_info . nl2br('\r\n'). $message );
				$mail->setFrom ( $email );
				$mail->addTo ( 'daniel.remeseiro@gmail.com', 'Daniel Remeseiro' ); //TODO get the user receiver email from ddbb
				$mail->setSubject ( 'nolotiro.org - private message from user ' . $subject ); //TODO translate and add the senders user name
				$mail->send ();

				$this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Message sent successfully!' ) );
				$this->_redirect ( '/'.$this->lang.'/ad/list/woeid/'.$this->aNamespace->location.'/ad_type/give' );

			}
		}
		// assign the form to the view
		$this->view->form = $form;
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
		require_once APPLICATION_PATH . '/forms/Message.php';
		$form = new Form_Message();
	
		return $form;
	}



}


