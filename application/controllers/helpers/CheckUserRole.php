<?php

class Zend_Controller_Action_Helper_CheckUserRole extends Zend_Controller_Action_Helper_Abstract {


         function check(){

             //get user id from auth object
             $auth = Zend_Auth::getInstance ();
             $userRole = null;

             if ($auth->hasIdentity()){
              $userId = $auth->getIdentity()->id;
              //fetch user role from ddbb
              require_once APPLICATION_PATH . '/models/User.php';
              $model = new Model_User();
              $userRole =  $model->fetchUser($userId)->role;
             }

              return $userRole;

        }

}
