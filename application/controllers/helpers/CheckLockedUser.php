<?php

class Zend_Controller_Action_Helper_CheckLockedUser extends Zend_Controller_Action_Helper_Abstract {


         function check(){

             $auth = Zend_Auth::getInstance ();
             $locked = null;

             if ($auth->hasIdentity()){

               //get user id from auth object
              $id = $auth->getIdentity()->id;

              //search on ddbb user locked value
              require_once APPLICATION_PATH . '/models/User.php';
              $model = new Model_User();
              $locked =  $model->CheckLockedUser( (int)$id) ;
             }

              return $locked;

        }

}

