<?php

/**
 * Nolotiro index controller - the default controller, showing the home page.
 * 
 */
class IndexController extends Zend_Controller_Action {

    public function init() {

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->user = Zend_Auth::getInstance ()->getIdentity();
        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();

        $this->view->checkMessages = $this->_helper->checkMessages->check();

        if ($this->view->checkMessages > 0) {
            $this->_helper->_flashMessenger->addMessage($this->view->translate('You have') . ' ' .
                    '<b><a href="/' . $this->view->lang . '/message/received">' . $this->view->translate('new messages') . ' (' . $this->view->checkMessages . ')</a></b>');
        }
    }

    public function indexAction() {


        //check if user is locked
        $locked = $this->_helper->checkLockedUser->check();
        if ($locked == 1) {
            $this->_redirect('/' . $this->view->lang . '/auth/logout');
        }


        $this->_redirect('/' . $this->view->lang . '/woeid/' . $this->location . '/give');
    }

}