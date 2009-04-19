<?php
/**
 * AuthController
 *  
 */

class AuthController extends Zend_Controller_Action
{
    public function init()
    {
        $this->initView();
        $this->view->baseUrl = $this->_request->getBaseUrl();
    }
        
	/**
     * The default action - show the home page
     */
    public function indexAction ()
    {    
        $this->_redirect('/');
    }

    
/**
     * Log in - show the login form or handle a login request
     * 
     * @todo Implement real authentication
     */
    public function loginAction()
    {
       	$request = $this->getRequest();
        $form = $this->_getUserLoginForm();
        
        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost()) {
            
            // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form
            if ($form->isValid($request->getPost())) {
                
                // collect the data from the user
                
                $f = new Zend_Filter_StripTags();
                $username = $f->filter($this->_request->getPost('username'));
                $password = $f->filter($this->_request->getPost('password'));
                
                
            //DDBB validation
            // setup Zend_Auth adapter for a database table
            $dbAdapter = Zend_Registry::get('dbAdapter');
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName('users');
            $authAdapter->setIdentityColumn('username');
            $authAdapter->setCredentialColumn('password');
            // Set the input credential values to authenticate against
            $authAdapter->setIdentity($username);
            $authAdapter->setCredential($password);
            // do the authentication
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            if ($result->isValid()) {
                // success: store database row to auth's storage
                // system. (Not the password though!)
                $data = $authAdapter->getResultRowObject(null,
                        'password');
                $auth->getStorage()->write($data);
                
                Zend_Session::regenerateId();
                $this->_helper->_flashMessenger->addMessage('You are now logged in, '.$username);
                $this->_redirect('/');
            } else {
                // failure: clear database row from session
                $view = $this->initView();                
                $view->error = 'Wrong user name or password, please try again';
                
                 
            }            
                
                //_redirect('/');
            }
        }
        // assign the form to the view
        $this->view->form = $form;
               
    
    }
    
	/**
	 * 
     * This method is essentially doing the same thing for the Form that we did 
     * above in the protected model accessor.  Same logic applies here.
     *
     * @return Form_UserLogin
     */
    protected function _getUserLoginForm()
    {
        require_once APPLICATION_PATH . '/forms/UserLogin.php';
        $form = new Form_UserLogin();
        $form->setAction($this->_helper->url('login'));
        return $form;
    }


}
