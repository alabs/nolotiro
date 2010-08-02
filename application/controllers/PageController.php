<?php

class PageController extends Zend_Controller_Action {


        public function init() {

        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->view->checkMessages  = $this->_helper->checkMessages->check();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->mensajes = $this->_flashMessenger->getMessages();


         //check if user is locked
        $locked = $this->_helper->checkLockedUser->check();
        if ($locked == 1) {
            $this->_redirect('/' . $this->view->lang . '/auth/logout');
        }

         if ($this->view->checkMessages > 0) {
            $this->_helper->_flashMessenger->addMessage($this->view->translate('You have') . ' ' .
                    '<b><a href="/' . $this->view->lang . '/message/received">' . $this->view->translate('new messages') . ' (' . $this->view->checkMessages . ')</a></b>');
        }
    }



         /*default action */
         public function indexAction(){

             $this->_redirect( '/');
       }

    
	public function tosAction() {


            $this->view->page_title .= $this->view->translate('Terms of service') ;
	}


        public function privacyAction() {


            $this->view->page_title .= $this->view->translate('Privacy') ;

	}


        public function faqsAction() {

            $this->view->page_title .= $this->view->translate('Frequently asked questions') ;

	}


        public function aboutAction() {


	}




}

