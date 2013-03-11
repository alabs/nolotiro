<?php

/**
 * @author David Rodríguez
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 *
 */
class MessageController extends Zend_Controller_Action {


    public function init() {
        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->check_messages = $this->_helper->CheckMessages;
        $this->notifications = $this->_helper->Notifications;
    }

    public function indexAction() {
        //dont do nothing, just redir to /
        $this->_redirect('/');
    }


    /**
     * Create a new conversation
     */
    public function createAction() {

        $request = $this->getRequest();

        $id_user_to = $request->getParam('id_user_to');
        $m_user = new Model_User();
        $object_user = $m_user->fetchUser($id_user_to);
        $this->view->user_to = $object_user->username;

        $f_message_create = new Form_MessageCreate();

        //first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {
            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->lang . '/message/create/id_user_to/' . $id_user_to . '/subject/' . $request->getParam('subject');
            $this->_redirect($this->lang . '/auth/login');
        }

        if ($this->getRequest()->isPost()) {

            if ($f_message_create->isValid($request->getPost())) {

                // collect the data from the user
                $f = new Zend_Filter_StripTags ( );
                $data['subject'] = $f->filter($this->_request->getPost('subject'));
                $data['body'] = $f->filter($this->_request->getPost('body'));

                if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                  $data['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } elseif (isset($_SERVER['REMOTE_ADDR'])) {
                  $data['ip'] = $_SERVER['REMOTE_ADDR'];
                }

                //get this ad user owner
                $data['user_from'] = $auth->getIdentity()->id;
                $data['user_to'] = $id_user_to;

                //get the username of the sender
                $m_user = new Model_User();
                $username_from = $m_user->fetchUser($data['user_from'])->toArray();

                // Create a new thread
                $m_message = new Model_Message();
                $id= $m_message->createThread($data);

                $mail = new Zend_Mail('utf-8');
                $hostname = 'http://' . $this->getRequest()->getHttpHost();

                $data['body'] = $data['subject'] . '<br/>' . $data['body'] . '<br/>';
                $data['body'] .= $this->view->translate('Go to this url to reply this message:') . '<br/>' .
                        '<a href="' . $hostname . '/' . $this->lang . '/message/received"> ' . $hostname . '/' . $this->lang . '/message/received</a>';
                $data['body'] .= '<br>---------<br/>';
                $data['body'] .= $this->view->translate('This is an automated notification. Please, don\'t reply  at this email address.');

                $mail->setBodyHtml($data['body']);
                $mail->setFrom('noreply@nolotiro.org', 'nolotiro.org');
                $mail->addTo($object_user->email);
                $mail->setSubject('[nolotiro.org] - ' . $this->view->translate('You have a new message from user') . ' ' . $username_from['username']);
                $mail->send();

                $this->_helper->_flashMessenger->addMessage($this->view->translate('Message sent successfully!'));
                $this->_redirect('/' . $this->lang . '/message/list');
            }
        } else {
            $data['subject'] = $this->_getParam('subject');

            $f_message_create->populate($data);
        }

        // assign the form to the view
        $this->view->form = $f_message_create;
    }


    /**
     * Add message to an existent conversation
     */
    public function replyAction() {

        $this->_helper->layout->disableLayout();
        $this->_helper->viewRenderer->setNoRender();

        $request = $this->getRequest();
        $id = $data['thread_id'] =  $request->getParam('id');
        $to = $data['user_to'] = $request->getParam('to');
        $lang = $this->lang;

        //first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {
            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $lang . '/message/reply/'.  $id . '/to/' . $to;
            $this->_redirect($lang . '/auth/login');
        }

        if ($request->isPost()) {

            // collect data
            $f = new Zend_Filter_StripTags ( );
            $data['body'] = $f->filter($request->getPost('body'));
            $data['user_from'] = $auth->getIdentity()->id;

            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
              $data['ip'] = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['REMOTE_ADDR'])) {
              $data['ip'] = $_SERVER['REMOTE_ADDR'];
            }

            // Insert new message in database
            $m_message = new Model_Message();
            $m_message->createMessage($data);

            // Send notification e-mail
            $mail = new Zend_Mail('utf-8');
            $hostname = 'http://' . $this->getRequest()->getHttpHost();
            $username_from = $auth->getIdentity()->username;
            $data['body'] = $data['subject'] . '<br/>' . $data['body'] . '<br/>';
            $data['body'] .= $this->view->translate(
                'Go to this url to reply this message:') . '<br/>
                <a href="' . $hostname . '/' . $this->lang . '/message/received">' .
                $hostname . '/' . $this->lang .  '/message/received</a>
                <br>---------<br/>';
            $data['body'] .= $this->view->translate(
                'This is an automated notification. Please, don\'t reply  at this email address.');
            $mail->setBodyHtml($data['body']);
            $mail->setFrom('noreply@nolotiro.org', 'nolotiro.org');
            $m_user = new Model_User();
            $object_user = $m_user->fetchUser($data['user_to']);
            $mail->addTo($object_user->email);
            $mail->setSubject('[nolotiro.org] - ' . $this->view->translate('You have a new message from user') . ' ' . $username_from);
            $mail->send();

            $this->_helper->_flashMessenger->addMessage($this->view->translate('Message sent successfully!'));
            $this->_redirect('/' . $this->lang . '/message/list');
        }

    }


    /**
     * List threads of a user
     */
    public function listAction() {

        $lang = $this->lang;

        // first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {

            // keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->getRequest()->getRequestUri();
            $this->_redirect($lang . '/auth/login');

        } else {

            $userid = $this->view->userid = $auth->getIdentity()->id;
            $this->view->username = $auth->getIdentity()->username;

            $m_message = new Model_Message();
            $this->view->list_threads = $m_message->getThreadsFromUser($userid);

            // paginator
            $page = $this->_getParam('page');
            $paginator = Zend_Paginator::factory($this->view->list_threads);
            $paginator->setDefaultScrollingStyle('Elastic');
            $paginator->setItemCountPerPage(10);
            $paginator->setCurrentPageNumber($page);

            $this->view->paginator = $paginator;
        }
    }


    /**
     * Show a conversation
     */
    public function showAction() {

        $id = $this->_request->getParam('id');
        $lang = $this->lang;
        $subject = $this->view->subject = $this->_request->getParam('subject');

        $m_message = new Model_Message();
        $this->view->thread = $m_message->getMessagesFromThread($id);

        if (!$this->view->thread) {
            $this->_helper->_flashMessenger->addMessage($this->view->translate('This thread does not exist!'));
            $this->_redirect('/' . $lang . '/woeid/' . $this->location . '/give');
            return;
        }

        $auth = Zend_Auth::getInstance ();
        if ($auth->hasIdentity()) {
            // if not the sender or receiver of the message, go to hell
            $this->view->me = $me = $auth->getIdentity()->id;
            // i need the username in the view
            $this->view->my_name = $auth->getIdentity()->username;
            $to = $this->view->thread[0]['user_to'];
            $from = $this->view->thread[0]['user_from'];
            if (($to != $me) && ($from != $me) ) {
                $this->_helper->_flashMessenger->addMessage($this->view->translate('You are not allowed to view this page'));
                $this->_redirect('/' . $lang . '/woeid/' . $this->location . '/give');
            }
        } else { //maybe the owner , but not logged, redir to login
                 //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $lang . '/message/show/' . $id . '/subject/' . $subject;
            $this->_redirect($lang . '/auth/login');
        }

        $m_message = new Model_Message();
        $m_message->markAsRead($id, $me);

        $reply_to = ($me == $to) ? $from : $to;
        $this->view->page_title .= $subject;

        $f_message_reply = new Form_MessageReply();

        $f_message_reply->setAction('/' . $lang . '/message/reply/' . $id .
                                    '/to/' . $reply_to);

        $this->view->createreply = $f_message_reply;
    }


    /**
     * Delete a conversation
     */
    public function deleteAction() {

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
        $lang = $this->lang;
        $location = $this->location;
        $request = $this->getRequest();

        /* User not logging, not allowed */
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {
            $this->_helper->_flashMessenger->addMessage(
                $this->view->translate('You are not allowed to view this page'));
            $this->_redirect('/' . $lang . '/woeid/' . $location . '/give');
            return;
        }

        $data['user_id'] = $auth->getIdentity()->id;
        $data['thread_id'] = (int)$request->getParam('id');

        $modelM = new Model_Message();
        $thread = $modelM->getThreadFromId($data['thread_id']);

        /* check current user is sender or recpient */
        if ($data['user_id'] == $thread->user_to ||
            $data['user_id'] == $thread->user_from) {

            $modelM->deleteThread($data);
            $this->_helper->_flashMessenger->addMessage(
                $this->view->translate('Message succesfully deleted'));
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $this->_redirect('/' . $lang . '/message/list');

        } else {

            $this->_helper->_flashMessenger->addMessage(
                $this->view->translate('You are not allowed to view this page'));
            $this->_redirect('/' . $lang . '/woeid/' . $location . '/give');
            return;
        }
    }

}
