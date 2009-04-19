<?php

/**
 * Nolotiro user controller - Handling user related actions - currently log in and
 * log out.
 * 
 */


class UserController extends Zend_Controller_Action
{
    
    protected $session = null;
	protected $_model;
    
	    
    /**
     * Overriding the init method to also load the session from the registry
     *
     */
    public function init()
    {
        parent::init();
        $this->session = Zend_Registry::get('session');
    }

    /**
     * Default action - if logged in, log out. If logged out, log in.
     *
     */
    public function indexAction()
    {
    	
    	
        if ($this->session->authenticated) {
            $this->_forward('logout');
        } else {
            $this->_forward('login');
        }
    }

    
    /**
     * Log out - delete user information and clear the session, then redirect to
     * the log in page.
     */
    public function logoutAction()
    {
        $this->session->logged_in = false;
        $this->session->username = false;
        
        $this->_redirect('/');
    }
    
    /**
     * register - register a new user into the nolotiro database
     */
    
 	public function registerAction()
    {
       	$request = $this->getRequest();
        $form    = $this->_getUserRegisterForm();
        
        

        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost()) {
            
            // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form
            if ($form->isValid($request->getPost())) {
                
                // since we now know the form validated, we can now
                // start integrating that data sumitted via the form
                // into our model
                $model = $this->_getModel();
                $model->save($form->getValues());
                
                //return $this->_helper->redirector('index');
               
//                $mail = new Zend_Mail();
//                $mail->setBodyText('This is the text of the mail.');
//                $mail->setFrom('noreply@nolotiro.com', 'nolotiro.com v2');
//                $mail->addTo('root', 'Daniel Remeseiro');
//                $mail->setSubject('nolotiro.com - confirm your email dear '.$form->email);
//                $mail->send();
                
                $this->_helper->_flashMessenger->addMessage('Check your inbox email to finish the register process');
                
                $this->_redirect('/');
            }
        }
        // assign the form to the view
        $this->view->form = $form;
        
    }
    
    /**
     * _getModel() is a protected utility method for this controller. It is 
     * responsible for creating the model object and returning it to the 
     * calling action when needed. Depending on the depth and breadth of the 
     * application, this may or may not be the best way of handling the loading 
     * of models.
     * Also note that since this is a protected method without the word 'Action',
     * it is impossible that the application can actually route a url to this 
     * method. 
     *
     * @return Model_User
     */
    protected function _getModel()
    {
        if (null === $this->_model) {
            // autoload only handles "library" components.  Since this is an 
            // application model, we need to require it from its application 
            // path location.
            require_once APPLICATION_PATH . '/models/User.php';
            $this->_model = new Model_User();
        }
        return $this->_model;
    }
    
    /**
     * This method is essentially doing the same thing for the Form that we did 
     * above in the protected model accessor.  Same logic applies here.
     *
     * @return Form_UserRegister
     */
    protected function _getUserRegisterForm()
    {
        require_once APPLICATION_PATH . '/forms/UserRegister.php';
        $form = new Form_UserRegister();
        $form->setAction($this->_helper->url('register'));
        return $form;
    }
    
	

	/**
     * Validate - check the token generated  sent by mail by registerAction, then redirect to
     * the logout  page (index home).
     * @param t
     * 
     */
    public function validateAction()
    {
        $token = $this->_request->getParam('t');//the token
        //http://nolotiro.com/user/validate/token234234234234234234
        
        
        if (!is_null($token)) {
            
            //TODO
            //add validation token against bbdd , if matches...
            $this->_helper->_flashMessenger->addMessage('Register finished succesfully,
             welcome to nolotiro '.$this->session->username);
          
            
            $this->_redirect('/user/logout');// redirect to logout action to kill the user logged in (if exists)
        	;
        }else {
        	$this->_helper->_flashMessenger->addMessage('Sorry, register url no valid or expired.');
        	$this->_redirect('/');
        }
        
        
        
        
              
        //$this->_redirect('/');
    }

}
