<?php

/**
 * ContactController
 *
 */

class ContactController extends Zend_Controller_Action {

    public function init() {
        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->check_messages = $this->_helper->checkMessages;
        $this->notifications = $this->_helper->Notifications;
    }


    /**
     * The default action - show the contact form
     */
    public function indexAction() {
        $request = $this->getRequest ();
        $form = $this->_getContactForm ();

        // check to see if this action has been POST'ed to
        if ($this->getRequest ()->isPost ()) {

            // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form
            if ($form->isValid ( $request->getPost () )) {

                // collect the data from the user
                $f = new Zend_Filter_StripTags ( );
                $email = $f->filter ( $this->_request->getPost ( 'email' ) );
                $message = $f->filter ( $this->_request->getPost ( 'message' ) );

                //get the username if its nolotiro user
                $user_info = $this->view->user->username;
                $user_info .= $_SERVER ['REMOTE_ADDR'];
                $user_info .= ' ' . $_SERVER ['HTTP_USER_AGENT'];

                $mail = new Zend_Mail ('utf-8');
                $body = $user_info.'<br/>'.$message;
                $mail->setBodyHtml ( $body );
                $mail->setFrom ( $email );
                $mail->addTo ( 'daniel.remeseiro@gmail.com', 'Daniel Remeseiro' );
                $mail->addTo ( 'hola@alabs.es', 'aLabs' );
                $mail->setSubject ( 'nolotiro.org - contact  from ' . $email );
                $mail->send ();

                $this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Message sent successfully!' ) );
                $this->_redirect ( '/'.$this->lang.'/woeid/'.$this->location.'/give' );
            }
        }
        $this->view->form = $form;
    }


    /**
     *
     * @return Form_Contact
     */
    protected function _getContactForm() {
        require_once APPLICATION_PATH . '/forms/Contact.php';
        $form = new Form_Contact ( );
        return $form;
    }

}
