<?php

class ErrorController extends Zend_Controller_Action {


    public function init() {
        $this->notifications = $this->_helper->Notifications;
    }


    public function errorAction() {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

            //eat that 404
            $this->lang =  $this->_helper->checklang->check();
            $this->location = $this->_helper->checklocation->check();
            $urlChunks = explode('/', $_SERVER['REQUEST_URI']);
            $urlChunks = str_replace('.html', '', $urlChunks);
            $urlChunks = str_replace('-', ' ', $urlChunks);
            //redir to search
            $this->_redirect('/' . $this->lang . '/search/?q=' . $urlChunks[sizeof($urlChunks) - 1] . '&ad_type=1&woeid=' . $this->location , array('code'=>301));
                break;
            default:
                // 500 error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $this->view->translate('500 Nolotiro.org Server error. Do not pray, fix it!');
                break;
        }
    }

}

