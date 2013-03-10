<?php

class Zend_Controller_Action_Helper_CheckWoeidUser extends Zend_Controller_Action_Helper_Abstract {


    function checkUserLogged($id){

        $model = new Model_User();
        $woeid =  $model->CheckWoeidUser($id) ;

        //set the cookie woeid to ddb value
        setcookie ( 'location', $woeid, null, '/' );

        //set the woeid value into session
        $aNamespace = new Zend_Session_Namespace('Nolotiro');
        $aNamespace->__set(location, $woeid);

        return $woeid;
    }

}

