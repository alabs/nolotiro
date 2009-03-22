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
     * Log in - show the login form or handle a login request
     * 
     * @todo Implement real authentication
     */
    public function loginAction()
    {
        if ($this->getRequest()->getMethod() != 'POST') {
            // Not a POST request, show log-in form
            $this->render();
        
        } else {
            // Handle log-in form
            $user = $this->getRequest()->getParam('user');
            $pass = $this->getRequest()->getParam('password');
            
            // TODO Fix here...
            if ($user == $pass) {
                $this->session->logged_in = true;
                $this->session->username = $user;
                
                Zend_Session::regenerateId();
                $this->_helper->_flashMessenger->addMessage('you are logged in now,'.$user);
                
                $this->_redirect('/');
                
            // Wrong user name / password
            } else {
                $view = $this->initView();
                $view->user = $user;
                $view->error = 'Wrong user name or password, please try again';
                
                $this->render();
            }
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
                
                // now that we have saved our model, lets url redirect
                // to a new location
                // this is also considered a "redirect after post"
                // @see http://en.wikipedia.org/wiki/Post/Redirect/Get
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
     * Validate - check the token generated and send by mail by registerAction via url, then redirect to
     * the log in page.
     */
    public function validateAction()
    {
        //$this->session->logged_in = false;
        //$this->session->username = false;
        $this->_helper->_flashMessenger->addMessage('register process finished succesfully, welcome to nolotiro');
                
        $this->_redirect('/');
    }

}
