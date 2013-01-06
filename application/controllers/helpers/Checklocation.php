<?php

class Zend_Controller_Action_Helper_Checklocation extends Zend_Controller_Action_Helper_Abstract {


    function init() {

        //first check the cookie
        if (isset($_COOKIE['location'])) {
          $this->location = $_COOKIE['location'];

        } else { // if cookie empty check session
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $this->location = $aNamespace->location;

            if ($this->location == null) {
                //TODO , check the location from GeoIP by default
                $this->location = 766273; // madrid, spain by default
                $aNamespace->location = $this->location;
                setcookie ('location', $this->location, null, '/' );
            }
        }
    }

    function check() {
        return $this->location;
    }
}
