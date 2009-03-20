<?php

/**
 * Zets index controller - the default controller, showing the home page.
 * 
 * @version $Id: IndexController.php,v 1.3 2007-12-04 16:54:49 seva Exp $
 */

require_once 'Zend/Controller/Action.php';
require_once 'Zend/Registry.php';

class IndexController extends Zend_Controller_Action
{

    /**
     * Override the init method to make sure no unauthorized users access
     * any action of this controller
     *
     */
    public function init()
    {
        parent::init();
        if (!Zend_Registry::get('session')->logged_in) {
            $this->_redirect('/user/login');
        }
    }

    public function indexAction()
    {
        $this->render();
    }
}