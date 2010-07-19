<?php
/**
 * @author Dani Remeseiro
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 *
 */

class MessageController extends Zend_Controller_Action {

    public function init() {

        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->view->checkMessages = $this->_helper->checkMessages->check();


        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->mensajes = $this->_flashMessenger->getMessages();

        if ($this->view->checkMessages > 0) {
            $this->_helper->_flashMessenger->addMessage($this->view->translate('You have') . ' ' .
                    '<b><a href="/' . $this->view->lang . '/message/received">' . $this->view->translate('new messages') . ' (' . $this->view->checkMessages . ')</a></b>');
        }
    }

    public function indexAction() {
        //dont do nothing, just redir to /
        $this->_redirect('/');
    }

    public function createAction() {

        $this->view->page_title .= $this->view->translate('send a private message');
        $request = $this->getRequest();
        $id_user_to = $this->_request->getParam('id_user_to');


        $model_user = new Model_User();
        $this->view->user_to = $model_user->fetchUser($id_user_to)->username;

        $form = $this->_getNewMessageForm();

        //first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {

            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->lang . '/message/create/id_user_to/' . $id_user_to;

            //Zend_Debug::dump($aNamespace->redir);
            $this->_redirect($this->lang . '/auth/login');
        }

        if ($this->getRequest()->isPost()) {

            if ($form->isValid($request->getPost())) {

                // collect the data from the user
                $f = new Zend_Filter_StripTags ( );
                $data['subject'] = $f->filter($this->_request->getPost('subject'));
                $data['body'] = $f->filter($this->_request->getPost('body'));


                if (getenv(HTTP_X_FORWARDED_FOR)) {
                    $data['ip'] = getenv(HTTP_X_FORWARDED_FOR);
                } else {
                    $data['ip'] = getenv(REMOTE_ADDR);
                }

                //get this ad user owner
                $data['user_from'] = $auth->getIdentity()->id;
                $data['user_to'] = $id_user_to;

                //get date created
                //TODO to use the Zend Date object to adapt the time to the locale user zone
                $data['date_created'] = date("Y-m-d H:i:s", time());

                //get the email of the receiver user
                $data['email'] = $this->_getModelMessage()->getEmailUser($id_user_to);

                //save the message into ddbb
                $modelMessage = $this->_getModelMessage()->save($data);


                $mail = new Zend_Mail('utf-8');
                $hostname = 'http://' . $this->getRequest()->getHttpHost();

                $data['body'] = $data['subject'] . '<br/>' . $data['body'] . '<br/>';
                $data['body'] .= $this->view->translate('Go to this url to reply this message:') .'<br/>'.
                       '<a href="'.$hostname.'/'.$this->lang. ' /message/received" ' .$hostname .'/'. $this->lang . '/message/received';
                $data['body'] .= '<br>---------<br/>';
                $data['body'] .= $this->view->translate('This is an automated notification. Please, don\'t reply  at this email address.');

                $mail->setBodyHtml($data['body']);
                $mail->setFrom('noreply@nolotiro.org', 'nolotiro.org');
                $mail->addTo($data['email']);
                $mail->setSubject('[nolotiro.org] - ' . $this->view->translate('You have a new message from user') . ' ' . $data['user_from']);
                $mail->send();

                $this->_helper->_flashMessenger->addMessage($this->view->translate('Message sent successfully!'));
                $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
            }
        }
        // assign the form to the view
        $this->view->form = $form;
    }

    public function receivedAction() {


        //first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {

            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->lang . '/message/received';
            $this->_redirect($this->lang . '/auth/login');
        } else { // if is user auth go to show received messages list
            $modelM = new Model_Message();
            $this->view->listmessages = $modelM->getMessagesUserReceived($auth->getIdentity()->id);


            //paginator
            $page = $this->_getParam('page');
            $paginator = Zend_Paginator::factory($this->view->listmessages);
            $paginator->setDefaultScrollingStyle('Elastic');
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($page);

            $this->view->paginator = $paginator;
        }
    }

    public function sentAction() {


        //first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {

            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->lang . '/message/sent';
            $this->_redirect($this->lang . '/auth/login');
        } else { // if is user auth go to show received messages list
            $modelM = new Model_Message();
            $this->view->listmessages = $modelM->getMessagesUserSent($auth->getIdentity()->id);


            //paginator
            $page = $this->_getParam('page');
            $paginator = Zend_Paginator::factory($this->view->listmessages);
            $paginator->setDefaultScrollingStyle('Elastic');
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($page);

            $this->view->paginator = $paginator;
        }
    }

    public function showAction() {

        $id = $this->_request->getParam('id');
        $model = $this->_getModelMessage();
        $this->view->message = $model->getMessage($id);

        $auth = Zend_Auth::getInstance ();
        if ($auth->hasIdentity()) { //not the owner of the message, go to hell
            if ($auth->getIdentity()->id != $this->view->message['user_to']) {
                $this->_helper->_flashMessenger->addMessage($this->view->translate('You are not allowed to view this page'));
                $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
            }
        } else { //maybe the owner , but not logged, redir to login
            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->lang . '/message/show/' . $id;
            $this->_redirect($this->lang . '/auth/login');
        }


        //**********************
        if ($this->view->message != null) { // if the id ad exists then render the message
            //set to readed
            $readed = new Model_Message();
            $readed->updateMessageReaded($id);

            $this->view->page_title .= $this->view->message['subject'];

            $form = $this->_getNewMessageForm();
            $form->setAction('/' . $this->lang . '/comment/create/ad_id/' . $id);
            $this->view->createcomment = $form;
        } else {

            $this->_helper->_flashMessenger->addMessage($this->view->translate('This message does not exist or may have been deleted!'));
            $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
        }
    }

    /**
     *
     * @return New_Message form
     */
    protected function _getNewMessageForm() {
        require_once APPLICATION_PATH . '/forms/Message.php';
        $form = new Form_Message();

        return $form;
    }

    /**
     * @return Model_Message
     */
    protected function _getModelMessage() {
        if (null === $this->_model) {

            require_once APPLICATION_PATH . '/models/Message.php';
            $this->_model = new Model_Message();
        }
        return $this->_model;
    }

    /**
     * @return Model_User
     */
    protected function _getModelUser() {
        if (null === $this->_model) {

            require_once APPLICATION_PATH . '/models/User.php';
            $this->_model = new Model_User();
        }
        return $this->_model;
    }

}

