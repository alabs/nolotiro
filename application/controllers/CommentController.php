<?php

class CommentController extends Zend_Controller_Action
{


    public function init()
    {
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->lang = $this->view->lang = $this->_helper->checklang->check();
    }



    public function createAction()
    {
        $request = $this->getRequest();
        $ad_id = $this->_request->getParam('ad_id');

        //first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance();
        if (!$auth->hasIdentity()) {
            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->lang . '/ad/' . $ad_id;

            $this->_redirect($this->lang . '/auth/login');

        } else {
            $form = $this->_getCommentForm();
            // check to see if this action has been POST'ed to
            if ($this->getRequest()->isPost()) {
                if ($form->isValid($request->getPost())) {
                    $formulario = $form->getValues();

                    //if comment its empty dont do nothing as redir to same ad
                    if (empty ($formulario['body'])) {
                        $this->_helper->_flashMessenger->addMessage($this->view->translate('Write something!'));
                        $this->_redirect('/' . $this->lang . '/ad/' . $ad_id);
                    }

                    //strip html tags to body
                    $formulario['body'] = strip_tags($formulario['body']);

                    //anti hoygan to body
                    $split = explode(". ", $formulario['body']);

                    foreach ($split as $sentence) {
                        $sentencegood = ucfirst(mb_convert_case($sentence, MB_CASE_LOWER, "UTF-8"));
                        $formulario['body'] = str_replace($sentence, $sentencegood, $formulario['body']);
                    }


                    //get the ip of the ad publisher
                    if (getenv(HTTP_X_FORWARDED_FOR)) {
                        $ip = getenv(HTTP_X_FORWARDED_FOR);
                    } else {
                        $ip = getenv(REMOTE_ADDR);
                    }

                    $formulario['ip'] = $ip;
                    $formulario['ads_id'] = $ad_id;

                    //get this ad user owner
                    $formulario ['user_owner'] = $auth->getIdentity()->id;

                    //get date created
                    //TODO to use the Zend Date object to apapt the time to the locale user zone
                    $datenow = date("Y-m-d H:i:s", time());
                    $formulario ['date_created'] = $datenow;


                    $modelC = new Model_Comment();
                    $modelC->save($formulario);


                    $this->_helper->_flashMessenger->addMessage($this->view->translate('Comment published succesfully!'));
                    $this->_redirect('/' . $this->lang . '/ad/' . $ad_id);
                }
            }
        }

    }

    public function editAction()
    {
        $request = $this->getRequest();
        $form = $this->_getCommentForm();

        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost()) {

            // now check to see if the form submitted exists, and
            // if the values passed in are valid for this form
            if ($form->isValid($request->getPost())) {
                $formulario = $form->getValues();
                //Zend_Debug::dump($formulario);

            }
        }
    }

    /**
     *
     * @return Form_AdEdit
     */
    protected function _getCommentForm()
    {
        require_once APPLICATION_PATH . '/forms/Comment.php';
        $form = new Form_Comment ();

        // assign the form to the view
        $this->view->form = $form;
        return $form;
    }

    public function deleteAction()
    {
    //TODO
    }





}
