<?php
/**
 * AdController
 *
 * @author  dani remeseiro
 * @abstract this is the Ad controller ,
 * do the crud relative to ads : create, show, edit, delete
 */
class AdController extends Zend_Controller_Action {

    public function init() {

        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->view->checkMessages  = $this->_helper->checkMessages->check();
        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->mensajes = $this->_flashMessenger->getMessages();


         //check if user is locked
        $locked = $this->_helper->checkLockedUser->check();
        if ($locked == 1) {
            $this->_redirect('/' . $this->view->lang . '/auth/logout');
        }

         if ($this->view->checkMessages > 0) {
            $this->_helper->_flashMessenger->addMessage($this->view->translate('You have') . ' ' .
                    '<b><a href="/' . $this->view->lang . '/message/received">' . $this->view->translate('new messages') . ' (' . $this->view->checkMessages . ')</a></b>');
        }
    }

    public function listAction() {

        $woeid = $this->_request->getParam('woeid');
        $ad_type = $this->_request->getParam('ad_type');

        if ($ad_type == 'give') {
            $this->view->page_title .= $this->view->translate('give') . ' | ';
        }

        if ($ad_type == 'want') {
            $this->view->page_title .= $this->view->translate('want') . ' | ';
        }

        $model = $this->_getModel();

        $this->view->woeid = $woeid;
        $this->view->ad = $model->getAdList($woeid, $ad_type);
        $this->view->woeidName = $this->_helper->woeid->name($woeid, $this->lang);
        $short = explode(',', $this->view->woeidName);
        $this->view->woeidNameShort = ' '. $this->view->translate('in') .' ' .$short[0];

        if (empty($this->view->ad)) {
            $this->view->suggestIP = $this->_helper->getLocationGeoIP->suggest();
        }

        
        //TODO , this sucks, do a better way to not show invalid woeids or null
        if ( (empty ($woeid) ) || ($woeid < 10) || ($woeid == 29370606) ) { //29370606 españa town
            $this->_helper->_flashMessenger->addMessage($this->view->translate('This location is not a valid town. Please, try again.'));
            $this->_redirect('/' . $this->lang . '/location/change');
        }


        //set the location name reg var from the woeid helper
        $aNamespace = new Zend_Session_Namespace('Nolotiro');
        $aNamespace->locationName = $this->view->woeidName;
        $this->view->page_title .= $this->view->woeidName;

        //paginator
        $page = $this->_getParam('page');
        $paginator = Zend_Paginator::factory($this->view->ad);
        $paginator->setDefaultScrollingStyle('Elastic');
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
    }

    public function listallAction() {

        $model = new Model_Ad();

        $this->view->woeid = $woeid;
        $this->view->ad = $model->getAdListAll();
        $this->view->page_title .= $this->view->translate('All the ads');

        //paginator
        $page = $this->_getParam('page');
        $paginator = Zend_Paginator::factory($this->view->ad);
        $paginator->setDefaultScrollingStyle('Elastic');
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;
    }

    public function listuserAction() {

        $id = (int) $this->_request->getParam('id');

        if ($id == null) {
            $this->_helper->_flashMessenger->addMessage($this->view->translate('this url does not exist'));
            $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
        }


        $model = $this->_getModel();
        $this->view->ad = $model->getAdUserlist($id);

        //paginator
        $page = $this->_getParam('page');
        $paginator = Zend_Paginator::factory($this->view->ad);
        $paginator->setDefaultScrollingStyle('Elastic');
        $paginator->setItemCountPerPage(10);
        $paginator->setCurrentPageNumber($page);

        $this->view->paginator = $paginator;


        require_once APPLICATION_PATH . '/models/User.php';
        $this->user = new Model_User();

        $this->view->user = $this->user->fetchUser($id);
    }

    public function showAction() {

        $id = $this->_request->getParam('id');
        $model = $this->_getModel();
        $this->view->ad = $model->getAd($id);

        //lets count the comments number and update
         $modelComments = new Model_Comment();
         $this->view->checkCountAd  = $count =  $modelComments->countCommentsAd( (int)$id);
         //let's increment +1 the ad view counter
         $model->updateReadedAd($id);
         $this->view->countReadedAd = $model->countReadedAd($id);
        

         if ($this->view->checkCountAd > 0) {
             $modelComments->updateCommentsAd($id, $count);
         }    

        if ($this->view->ad != null) { // if the id ad exists then render the ad and comments

            if ($this->view->ad['type'] == 'give') {
                $this->view->page_title .= $this->view->translate('give') . ' | ';
            }

             if ($this->view->ad['type'] == 'want') {
                $this->view->page_title .= $this->view->translate('want') . ' | ';
            }

            $this->view->comments = $model->getComments($id);
            $this->view->woeidName = $this->_helper->woeid->name($this->view->ad['woeid_code'], $this->lang);
            $this->view->page_title .= $this->view->woeidName . ' | ' . $this->view->ad['title'];

            //if user logged in, show the comment form, if not show the login link
            $auth = Zend_Auth::getInstance ();
            if (!$auth->hasIdentity()) {

                $this->view->createcomment = '<a href="/' . $this->lang . '/auth/login">' . $this->view->translate('login to post a comment') . '</a> ';
            } else {
                require_once APPLICATION_PATH . '/forms/Comment.php';
                $form = new Form_Comment();
                $form->setAction('/' . $this->lang . '/comment/create/ad_id/' . $id);

                $this->view->createcomment = $form;
            }
        } else {

            $this->_helper->_flashMessenger->addMessage($this->view->translate('This ad does not exist or may have been deleted!'));
            $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
        }
    }

    public function createAction() {

        //first we check if user is logged, if not redir to login
        $auth = Zend_Auth::getInstance ();
        if (!$auth->hasIdentity()) {

            //keep this url in zend session to redir after login
            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->redir = $this->lang . '/ad/create';

            //Zend_Debug::dump($aNamespace->redir);
            $this->_redirect($this->lang . '/auth/login');
        } else {

            $request = $this->getRequest();
            require_once APPLICATION_PATH . '/forms/AdCreate.php';
            $form = new Form_AdCreate( );

            $this->view->form = $form;
            $this->view->woeidName = $this->_helper->woeid->name($this->location, $this->lang);


            if ($this->getRequest()->isPost()) {

                if ($form->isValid($request->getPost())) {

                    $formulario = $form->getValues();

                    //create thumbnail if image exists
                    if (!empty($formulario['photo'])) {

                        $photobrut = $formulario['photo'];
                        $formulario['photo'] = $this->_createThumbnail($photobrut, '100', '90');
                    }


                    // Create a filter chain and add filters to title and body against xss, etc
                    $f = new Zend_Filter();
                    $f->addFilter(new Zend_Filter_StripTags());
                    //->addFilter(new Zend_Filter_HtmlEntities());

                    $formulario['title'] = $f->filter($formulario['title']);
                    $formulario['body'] = $f->filter($formulario['body']);

                    //anti HOYGAN to title
                    //dont use strtolower because dont convert utf8 properly . ej: á é ó ...
                    $formulario['title'] = ucfirst(mb_convert_case($formulario['title'], MB_CASE_LOWER, "UTF-8"));

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

                    //get this ad user owner
                    $formulario ['user_owner'] = $auth->getIdentity()->id;

                    //get date created
                    //TODO to use the Zend Date object to apapt the time to the locale user zone
                    $datenow = date("Y-m-d H:i:s", time());
                    $formulario ['date_created'] = $datenow;

                    //get woeid to assign to this ad
                    //the location its stored at session location value
                    //(setted by default on bootstrap to Madrid woeid number)

                    $formulario ['woeid_code'] = $this->location;

                    $model = $this->_getModel();
                    $model->createAd($formulario);

                    $this->_helper->_flashMessenger->addMessage($this->view->translate('Ad published succesfully!'));
                    $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
                }
            }
        }
    }

    public function editAction() {

        //check if user logged in
        $auth = Zend_Auth::getInstance ();
        $user = new Model_User;

        if ($auth->hasIdentity()) {
            //if user owner allow edit and show delete ad link , if not redir not allowed
            // var_dump( (bool) $user->fetchUser($auth->getIdentity()->id) );

            if (!(bool) $user->fetchUser($auth->getIdentity()->id)) {
                $this->_helper->_flashMessenger->addMessage($this->view->translate('You are not allowed to do that!'));
                $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
            }
        } else {

            $this->_helper->_flashMessenger->addMessage($this->view->translate('You are not allowed to do that!'));
            $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
        }

        $this->view->deletead = '
                    <a href="/' . $this->view->lang . '/ad/delete/id/' . $this->_getParam('id') . ' ">' . $this->view->translate('delete this ad') . '</a>';


        $request = $this->getRequest();
        require_once APPLICATION_PATH . '/forms/AdEdit.php';
        $form = new Form_AdEdit ( );
        

        $form->addElement('select', 'status', array(
            'order' => '1',
            'label' => 'Status:', 'required' => true,
            'multioptions' => array('available' => 'available', 'booked' => 'booked', 'delivered' => 'delivered')));



        $this->view->page_title .= $this->view->translate('Edit your ad');
        $this->view->form = $form;

       if ($this->getRequest()->isPost()) {

           $formData = $this->getRequest()->getPost();


                if ($form->isValid($formData)) {

            $id = (int) $this->getRequest()->getParam('id');

            $formulario = $form->getValues();
            //var_dump($form);

                //set filter againts xss and nasty things
                $f = new Zend_Filter();
                $f->addFilter(new Zend_Filter_StripTags());

                $data['title'] = $f->filter($formulario['title']);
                $data['body'] = $f->filter($formulario['body']);
                $data['type'] = $f->filter($formulario['type']);

                //create thumbnail if image exists
                if ($formulario['photo']) {

                    $photobrut = $formulario['photo'];
                    $data['photo'] = $this->_createThumbnail($photobrut, '100', '90');

                    
                }


                $data['status'] = $formulario['status'];               
                $data['comments_enabled'] = $formulario['comments_enabled'];

                $model = $this->_getModel();
                $model->updateAd( $data, $id);

                $this->_helper->_flashMessenger->addMessage($this->view->translate('Ad edited succesfully!'));
                $this->_redirect('/' . $this->lang . '/ad/show/id/' . $id);
            } else {

                $id = $this->_getParam('id');
                 $ad = new Model_Ad();

                $advalues = $ad->getAd($id);
                // if photo not empty then show and let change it
                $current_photo = $advalues['photo'];
                if ($current_photo) {
                    $this->view->current_photo = ' <img alt="' . $title . '" src="/images/uploads/ads/100/' . $current_photo . '" />';
                }

                $form->populate($formData);
            }
        } else {
            $id = $this->_getParam('id');
            if ($id > 0) {
                $ad = new Model_Ad();

                $advalues = $ad->getAd($id);
                // if photo not empty then show and let change it

                $current_photo = $advalues['photo'];

                if ($current_photo) {
                    $this->view->current_photo = ' <img alt="' . $title . '" src="/images/uploads/ads/100/' . $current_photo . '" />';
                }
                $form->populate($ad->getAd($id));
            }
        }
        
    }



    /*
     * _createThumbnail uses resize class
     *
     */

    protected function _createThumbnail($file, $x, $y) {

        require_once ( NOLOTIRO_PATH . '/library/SimpleImage.php' );

        $file_ext = substr(strrchr($file, '.'), 1);
        $fileuniquename = md5(uniqid(mktime())) . '.' . $file_ext;

        $image = new SimpleImage();
        $image->load('/tmp/' . $file);

        //save original to right place
        $widthmax = 900;
        $image->resizeToWidthMax($widthmax);
        $image->save(NOLOTIRO_PATH . '/www/images/uploads/ads/original/' . $fileuniquename);

        //save thumb 100
        $image->resizeToWidth($x);
        $image->save(NOLOTIRO_PATH . '/www/images/uploads/ads/100/' . $fileuniquename);

        return $fileuniquename;
    }

    public function deleteAction() {
        $this->view->headTitle()->append($this->view->translate('Delete your profile'));

        $id = (int) $this->getRequest()->getParam('id');
        $auth = Zend_Auth::getInstance ();

        if ($auth->hasIdentity()) {

            $umodel = new Model_User();
            $user = $umodel->fetchUser($auth->getIdentity()->id);
        } else {

            $this->_helper->_flashMessenger->addMessage($this->view->translate('You are not allowed to view this page'));
            $this->_redirect('/' . $this->view->lang . '/ad/list/woeid/' . $this->location . '/ad_type/give');
            return;
        }

        if (($auth->getIdentity()->id == $user)) { //if is the user profile owner lets delete it
            if ($this->getRequest()->isPost()) {
                $del = $this->getRequest()->getPost('del');
                if ($del == 'Yes') {
                    //delete user, and all his content
                    $admodel = new Model_Ad();
                    $admodel->deleteAd($id);

                    $this->_helper->_flashMessenger->addMessage($this->view->translate('Ad deleted successfully.'));
                    $this->_redirect('/' . $this->view->lang . '/ad/list/woeid/' . $this->location . '/ad_type/give');
                    return;
                } else {
                    $this->_helper->_flashMessenger->addMessage($this->view->translate('Nice to hear that :-)'));
                    $this->_redirect('/' . $this->view->lang . '/ad/list/woeid/' . $this->location . '/ad_type/give');
                    return;
                }
            } else {
                $id = $this->_getParam('id', 0);
            }
        } else {

            $this->_helper->_flashMessenger->addMessage($this->view->translate('You are not allowed to view this page'));
            $this->_redirect('/' . $this->view->lang . '/ad/list/woeid/' . $this->location . '/ad_type/give');
            return;
        }
    }

    protected function _getModel() {
        if (null === $this->_model) {

            require_once APPLICATION_PATH . '/models/Ad.php';
            $this->_model = new Model_Ad ( );
        }
        return $this->_model;
    }

}