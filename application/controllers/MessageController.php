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
	}


        public function createAction(){

            $id_user_to = $this->_request->getParam ( 'id_user_to' );

        }

        public function listAction(){

        }

        public function checkAction(){

        }
}
?>

