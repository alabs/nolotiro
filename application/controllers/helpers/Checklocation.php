<?php

class Zend_Controller_Action_Helper_Checklocation extends Zend_Controller_Action_Helper_Abstract {


    function init() {

        //first check the cookie
        $this->location = $_COOKIE['location'];

        //if cookie empty check session
        if ($this->location == null)
        {
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $this->location = $aNamespace->location;

            if ($this->location == null) {
                    //TODO , check the location from GeoIP by default
                    $this->location = 766273; // madrid, spain by default
                    $aNamespace->__set($location, $this->location);
                    setcookie ( 'location', $this->location, null, '/' );
                    }
        }
        
    }

    function check() {
        return $this->location;
    }
}