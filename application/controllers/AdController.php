<?php

/**
 * AdController
 * 
 * @author
 * @abstract this is the Ad controller , 
 * do the crud relative to ads : create, show, edit, delete
 */


class AdController extends Zend_Controller_Action {
	
	/**
     * Overriding the init method to also load the session from the registry
     *
     */
    public function init()
    {
        parent::init();
        //$this->view->baseUrl = $this->_request->getBaseUrl();
        
        $this->view->baseUrl = Zend_Controller_Front::getParam($route);
        
    }


	/**
	 * The default action - show the home page
	 */
	public function indexAction() {
		
		//by now just redir to /
    	$this->_redirect('/');
	}

	
	
	public function showAction() {
		
		
	}
	
	public function editAction(){
		$request = $this->getRequest();
        $form    = $this->_getAdEditForm();
        

        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost()) {
            
            // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form
            if ($form->isValid($request->getPost())) {
                
                // since we now know the form validated, we can now
                // start integrating that data submitted via the form
                // into our model
                $formulario = $form->getValues();
                //Zend_Debug::dump($formulario);
		
            }
        }
	}
	
	/**
     *
     * @return Form_AdEdit 
     */
    protected function _getAdEditForm()
    {
        require_once APPLICATION_PATH . '/forms/AdEdit.php';
        $form = new Form_AdEdit();
        
        // assign the form to the view
        $this->view->form = $form;
        return $form;
    }
	
	public function deleteAction(){
		
	}
}
