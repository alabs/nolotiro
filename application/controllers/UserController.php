<?php
/**
 * Nolotiro user controller - Handling user related actions
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
        //$this->view->baseUrl = $this->_request->getBaseUrl();
        
        $this->view->baseUrl = Zend_Controller_Front::getParam($route);
        
    }

    /**
     * Default action - if logged in, go to profile. If logged out, go to register.
     *
     */
    public function indexAction()
    {
    	//by now just redir to /
    	$this->_redirect('/');
//        if ($this->session->authenticated) {
//            $this->_forward('profile');
//        } else {
//            $this->_forward('register');
//        }
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
                // start integrating that data submitted via the form
                // into our model
                $formulario = $form->getValues();
                Zend_Debug::dump($formulario);
                
                $model = $this->_getModel();
                
                //check user email and nick if exists
                $checkemail = $model->checkEmail($formulario['email']);
                $checkuser = $model->checkUsername($formulario['username']);
                
                
                if ($checkemail !== NULL ) {
                	$view = $this->initView();
                    $view->error = $this->view->translate('This email is taken. Please, try again.'); 
                    
                } 
                
                if ($checkuser !== NULL){
                    $view = $this->initView();
                    $view->error = $this->view->translate('This username is taken. Please, try again.');
                    
                }
                
                if ($checkemail == NULL and  $checkuser == NULL ) { 
                    
                    // success: insert the new user on ddbb
                    $model->save($form->getValues());
                    

                    //now lets send the confirm token by email to confirm the user email
                                    
                    $mail = new Zend_Mail();
                    $mail->setBodyHtml($this->view->translate('Please, click on this url to finish your register process:<br />')
                    .$this->baseUrl.'http://nolotiro/user/validate/t/1231298742938472938479');
                    $mail->setFrom('noreply@nolotiro.com', 'nolotiro.com');
                    
                    //$mail->addTo('daniel.remeseiro@gmail.com');
                    $mail->addTo($formulario['email']);
                    $mail->setSubject($formulario['username'].$this->view->translate(', confirm your email'));
                    $mail->send();
                    
                    $this->_helper->_flashMessenger->addMessage($this->view->translate('Check your inbox email to finish the register process'));
                    
                    $this->_redirect('/');
                    
                    //return $this->_helper->redirector('index');
                    
                    
                }
                
               
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
     *
     * @return Form_UserRegister
     */
    protected function _getUserRegisterForm()
    {
        require_once APPLICATION_PATH . '/forms/UserRegister.php';
        $form = new Form_UserRegister();
        return $form;
    }

    
    /**
     * forgot - sends (resets) a new password to the user 
     * 
     */
    
 	public function forgotAction()
    {
       	$request = $this->getRequest();
        $form    = $this->_getUserForgotForm();
         
        if ($this->getRequest()->isPost()) {
            
            if ($form->isValid($request->getPost())) {
                
                // collect the data from the form
                $f = new Zend_Filter_StripTags();
                $email = $f->filter($this->_request->getPost('email'));
                    
                
                $model = $this->_getModel();
                $mailcheck = $model->checkEmail($email);
                
                if ($mailcheck == NULL) {
                	// failure: email does not exists on ddbb
                    $view = $this->initView();
                    $view->error = $this->view->translate('This email is not in our database. Please, try again.');
                	
                } else { // success: the email exists , so lets change the password and send to user by mail
                  //Zend_Debug::dump($mailcheck->toArray());
                  $mailcheck = $mailcheck->toArray();
                
                  //update the ddbb with new password  
                  $data['password'] = $this->_generatePassword();
                  $data['id'] = $mailcheck['id'];
                  
                  //Zend_Debug::dump($data);
                  $model->update($data);
  
                  //lets send the new password..
                  $mail = new Zend_Mail();
                  $mail->setBodyHtml(utf8_decode($this->view->translate('Hi, this is your new password:<br />')).
                  utf8_decode($this->view->translate('User name:')).$mailcheck['username'].'<br />'.
                  utf8_decode($this->view->translate('Password:')).$data['password']);
                  $mail->setFrom('noreply@nolotiro.com', 'nolotiro.com');
                
                  
                  $mail->addTo($mailcheck['email']);
                  $mail->setSubject(utf8_decode($this->view->translate('Your nolotiro.com new password')));
                  $mail->send();
                
                  $this->_helper->_flashMessenger->addMessage($this->view->translate('Check your inbox email to get your new password'));
                
                  $this->_redirect('/');
                }
                
            }
        }
        // assign the form to the view
        $this->view->form = $form;
        
    }
    
    
    /**
     * @abstract generate a text plain random password
     * remember it's no encrypted !
     * @return string (7) $pass
     */
    protected function _generatePassword()
    { 
        $salt = "abcdefghjkmnpqrstuvwxyz123456789";
        srand((double)microtime()*1000000);
        $i = 0;
        while ($i <= 6) {
           $num = rand() % 33;
           $pass .= substr($salt, $num, 1);
           $i++;
        }
       
       return $pass;
    }
    
    
	/**
	 *
     * @return Form_UserForgotForm
     */
    protected function _getUserForgotForm()
    {
        require_once APPLICATION_PATH . '/forms/UserForgot.php';
        $form = new Form_UserForgot();
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
        //http://nolotiro.com/es/auth/validate/t/1232452345234
        
        if (!is_null($token)) {
            
            //TODO
            //add validation token against bbdd , if matches...

            //DDBB validation
            // setup Zend_Auth adapter for a database table
            $dbAdapter = Zend_Registry::get('dbAdapter');
            $authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
            $authAdapter->setTableName('users');
            $authAdapter->setIdentityColumn('token');
                 
            // Set the input credential values to authenticate against
            $authAdapter->setIdentity($token);
            
            // do the authentication
            $auth = Zend_Auth::getInstance();
            $result = $auth->authenticate($authAdapter);
            
            if ($result->isValid()) {
            //OK
            $this->_helper->_flashMessenger->addMessage('Register finished succesfully, welcome to nolotiro '.$this->session->username);
          
            
            $this->_redirect('/es/auth/logout');// redirect to logout action to kill the user logged in (if exists)
            }else {
                   $this->_helper->_flashMessenger->addMessage('Sorry, this validation url does not exists.');
        	       $this->_redirect('/');
                     
                }     
            
            
            
            
            
        }else {
        	$this->_helper->_flashMessenger->addMessage($this->view->translate('Sorry, register url no valid or expired.'));
        	$this->_redirect('/');
        }
        
        
        
        
              
        //$this->_redirect('/');
    }

	
}
