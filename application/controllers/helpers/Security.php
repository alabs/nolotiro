<?php

class Zend_Controller_Action_Helper_Security extends Zend_Controller_Action_Helper_Abstract {




    function badparams() {

        // see a lot of requests using underscore param , possibly bots scraping nolotiro ads
        if($this->getRequest()->getParam('_')){
            die('You are not human. Please, use the rss feeds to fetch nolotiro ads');
        }

        return;

    }


}

