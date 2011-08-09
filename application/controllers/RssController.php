<?php

class RssController extends Zend_Controller_Action {

    public function init() {
        $this->lang = $this->view->lang = $this->_helper->checklang->check();
    }



    public function preDispatch()  {
        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout()->disableLayout();
    }


    public function feedAction() {

        $woeid = $this->_request->getParam('woeid');
        $ad_type = $this->_request->getParam('ad_type');
        $status = $this->_request->getParam('status');

        $modelAd = new Model_Ad();
        $this->ads = $modelAd->getAdList($woeid, $ad_type, $status , 35);

        $rss['title'] = $this->view->translate($ad_type).  ' ' . $this->view->translate( (string)$status ). ' - '. $this->_helper->woeid->name($woeid, $this->lang) .' | nolotiro.org';
        $rss['link'] = 'http://' . $_SERVER['HTTP_HOST'] .'/'.$this->lang.'/rss/feed/woeid/'.$woeid.'/ad_type/'.$ad_type . '/status/' . $status;
        $rss['charset'] = 'utf-8';
        $rss['description'] = 'nolotiro.org - '. $this->_helper->woeid->name($woeid, $this->lang);
        $rss['language'] = $this->lang;
        $rss['generator'] = 'nolotiro.org';
        $rss['entries'] = array();

        foreach ($this->ads as $value) {

            $entry = array();
            $entry['title'] = $value['title'];
            $entry['link'] = 'http://' . $_SERVER['HTTP_HOST'] .'/'.$this->lang.'/ad/show/id/'.$value['id'].'/'.$value['title'];
            $entry['description'] = $value['body'];
            $entry['lastUpdate'] = strtotime($value['date_created']);

            $rss['entries'][]  = $entry;

        }

        $feedObj = Zend_Feed::importArray($rss, 'rss');
        return $feedObj->send();
    }
}