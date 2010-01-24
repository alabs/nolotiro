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
				$data['email'] = $f->filter ( $this->_request->getPost ( 'email' ) ); //TODO get the email  sender user  from ddbb
                                $data['subject'] = $f->filter ( $this->_request->getPost ( 'subject' ) );
				$data['body'] = $f->filter ( $this->_request->getPost ( 'body' ) );


                                //TODO keep the message in messages ddbb table here, make model
                                //get the ip of the ad publisher
					if (getenv(HTTP_X_FORWARDED_FOR)) {
					    $data['ip'] = getenv(HTTP_X_FORWARDED_FOR);
					} else {
					    $data['ip'] = getenv(REMOTE_ADDR);
					}

					//get this ad user owner
					$data['user_from'] = $auth->getIdentity ()->id;
                                        $data['user_to'] = $id_user_to;

					//get date created
					//TODO to use the Zend Date object to adapt the time to the locale user zone
					$data['date_created'] = date("Y-m-d H:i:s", time() );


				//get the username if its nolotiro user
				//$user_info = $this->view->user->username;

                                //keep the message into ddbb
                                $modelMessage = $this->_getModelMessage();
				$modelMessage->save ( $data );



				$mail = new Zend_Mail ('utf-8' );

                                $data['body'] = $data['subject'] .'<br/>'. $data['body'].'<br/>';
                                $data['body'] .= '-------------------------------------------<br/>';
                                $data['body'] .= $this->view->translate('This is a private message sent from nolotiro.org');
                                
				$mail->setBodyHtml( $data['body'] );
				$mail->setFrom ( $email );
				$mail->addTo ( 'daniel.remeseiro@gmail.com', 'Daniel Remeseiro' ); //TODO get the user receiver email from ddbb
				$mail->setSubject ( '[nolotiro.org] - '.$this->view->translate('private message from user ') . $data['subject'] ); //TODO translate and add the senders user name
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




        /**
	 * @return Model_Message
	 */
	protected function _getModelMessage() {
		if (null === $this->_model) {

			require_once APPLICATION_PATH . '/models/Message.php';
			$this->_model = new Model_Message();
		}
		return $this->_model;
	}


        /**
	 * @return Model_User
	 */
	protected function _getModelUser() {
		if (null === $this->_model) {

			require_once APPLICATION_PATH . '/models/User.php';
			$this->_model = new Model_User();
		}
		return $this->_model;
	}



}


