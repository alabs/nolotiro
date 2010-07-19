<?php

class ErrorController extends Zend_Controller_Action
{

    public function init(){
        //$this->_helper->layout()->setLayout('error');
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
    }



    public function errorAction()
    {
        $errors = $this->_getParam('error_handler');

        switch ($errors->type) {
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ROUTE:

                 $this->view->lang =  $this->_helper->checklang->check();
                 $this->_redirect('/'.$this->view->lang.'/');

            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_CONTROLLER:
            case Zend_Controller_Plugin_ErrorHandler::EXCEPTION_NO_ACTION:

                // 404 error -- controller or action not found
//                $this->view->headTitle()->append(' - ');
//                $this->view->headTitle()->append('error 404');
                $this->getResponse()->setHttpResponseCode(301);
//                $this->view->message = '404 Page not found.';


                $this->_helper->_flashMessenger->addMessage($this->view->translate('Page not found'));
                    $this->_redirect('/');

                break;
            default:
                // 500 error
                $this->getResponse()->setHttpResponseCode(500);
                $this->view->message = $this->view->translate('500 Server error.');
                break;
        }

        // Log exception, if logger available
        if ($log = $this->getLog()) {
            $log->crit($this->view->message, $errors->exception);
        }

        // conditionally display exceptions
        if ($this->getInvokeArg('displayExceptions') == true) {
            $this->view->exception = $errors->exception;
        }

        $this->view->request   = $errors->request;
    }

    public function getLog()
    {
        $bootstrap = $this->getInvokeArg('bootstrap');
        if (!$bootstrap->hasPluginResource('Log')) {
            return false;
        }
        $log = $bootstrap->getResource('Log');
        return $log;
    }


}

