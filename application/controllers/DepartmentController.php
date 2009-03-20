<?php

/**
 * Zets department controller - handling department related actions 
 * (currently mostly showing departments)
 *  
 * @version $Id: DepartmentController.php,v 1.3 2007-12-04 16:54:49 seva Exp $
 */

require_once 'Zend/Registry.php';
require_once 'application/models/Nolotiro/Department.php';
require_once 'Zend/Controller/Action.php';

class DepartmentController extends Zend_Controller_Action
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
        $this->_forward('index', 'index');
    }

    public function showAction()
    {
        $name = $this->getRequest()->getParam('name');
        $view = $this->initView();
        
        if ($name) {
            $dept = Zets_Department::getDepartmentByName($name);
            if ($dept) {
                // Department was found
                $view->deptname = $dept->getName();
                $view->subdepts = $dept->getChildDepts();
                $view->employees = $dept->getEmployees();
            
            } else {
                // No department by that name was found...
                $view->error = 'The department "' . $name . '" does not exist in ' . 'this company. Please choose a department from the list.';
            }
        
        } else {
            // No department name specified
            $view->error = 'No department name was specified. Please choose a ' . 'department from the list.';
        }
        
        $this->render();
    }
}