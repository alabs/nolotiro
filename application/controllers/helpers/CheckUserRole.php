<?php

class Zend_Controller_Action_Helper_CheckUserRole extends Zend_Controller_Action_Helper_Abstract {

    function check(){

        //get user id from auth object
        $auth = Zend_Auth::getInstance ();
        $userRole = null;

        if ($auth->hasIdentity()){
            $userId = $auth->getIdentity()->id;
            //fetch user role from ddbb
            $model = new Model_User();
            $user = $model->fetchUser($userId);
            if ($user)
               $userRole =  $user->role;
        }

         return $userRole;

    }

}
