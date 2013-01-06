<?php

class IndexController extends Zend_Controller_Action {


    public function init() {
        $this->view->user = Zend_Auth::getInstance ()->getIdentity();
        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->check_messages = $this->_helper->checkMessages;
        $this->notifications = $this->_helper->Notifications;
    }


    public function indexAction() {

       $this->_helper->layout()->setLayout('home');
       $this->view->suggestIP = $this->_helper->getLocationGeoIP->suggest();

        //check if user is locked
        $locked = $this->_helper->checkLockedUser->check();
        if ($locked == 1) {
            $this->_redirect('/' . $this->view->lang . '/auth/logout');
        }

        //if user is logged the redir to proper location, if not stand on not logged home view (index)
        $auth = Zend_Auth::getInstance();

        if ($auth->hasIdentity()) {
            $this->_redirect('/' . $this->view->lang . '/woeid/' . $this->location . '/give');
        }

        //check if request is / redir to /lang
        $langIndex = $this->getRequest()->getParam('language');

        if($langIndex == null){
            //add meta robots to not index the page without language param but allow follow-crawl all the rest
            //$this->view->metaRobots = 'noindex,follow';
            //force redirect with language
            $this->_redirect('/es' , array('code' => 301) );
        } else{
            $this->view->metaRobots = 'index,follow';
        }

        //add link rel canonical , better seo
        $this->view->canonicalUrl = 'http://' . $_SERVER['HTTP_HOST'] . '/' . $this->lang ;


        $modelAd = new Model_Ad();
        $this->view->allGives = $modelAd->getAdListAllHome(1, null);
        $this->view->rankingWoeid = $modelAd->getRankingWoeid($limit=170);
        $this->view->rankingUsers = $modelAd->getRankingUsers($limit=80);

        //add meta description to head
        $this->view->metaDescription = $this->view->translate('nolotiro.org is a website where you can give away things you no longer want or no longer need to pick them up other people who may serve or be of much use.');
    }


    public function setlangAction() {
        $this->referer = $_SERVER['HTTP_REFERER'];
        $lang = $this->_getParam("language");
        $auth = Zend_Auth::getInstance();
        if ($auth->hasIdentity()) {
            $umodel = new Model_User();
            $data = (array)$auth->getIdentity();
            $data['lang'] = $lang;
            $umodel->update($data);
            $auth->getStorage()->write((object)$data);
        }

        setcookie ( "lang", $lang, null, '/' );

        if ($this->hasValidReferer()) {
            $new_url = explode("/", $this->referer);
            if (count($new_url)>3 && strlen($new_url[3])>0) $new_url[3] = $lang;
            $this->_redirect(join("/",$new_url),  array('code' => 301));

        }
        else
            $this->_redirect ( '/' , array('code' => 301));
    }


    function hasValidReferer() {
        if (!$this->referer) return false;

        # invalid if is the same URL
        $currentURI = $_SERVER['SCRIPT_URI'];
        if (strcmp($this->referer, $currentURI) == 0) return false;

        # invalid if is not in this site
        $barpos = strpos($currentURI, "/", 8);
        if (strncmp($this->referer, $currentURI, $barpos ) != 0) return false;

        return true;
    }

}
