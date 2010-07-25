<?php

class Zend_Controller_Action_Helper_CheckLockedUser extends Zend_Controller_Action_Helper_Abstract {


         function check(){

             $auth = Zend_Auth::getInstance ();
             $id = $auth->getIdentity()->id;

              //search on ddbb user locked value
              require_once APPLICATION_PATH . '/models/User.php';
              $model = new Model_User();
              $locked =  $model->CheckLockedUser( (int)$id) ;


              return $locked;

        }

}

