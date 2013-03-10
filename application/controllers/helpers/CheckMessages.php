<?php

class Zend_Controller_Action_Helper_CheckMessages extends Zend_Controller_Action_Helper_Abstract {


    function check() {

        $auth = Zend_Auth::getInstance ();
        $checkMessages = null;
        if ($auth->hasIdentity()) {

            $modelM = new Model_Message();
            $checkMessages = $modelM->checkMessagesUser($auth->getIdentity()->id);
        }

        return $checkMessages;

    }

   
}
