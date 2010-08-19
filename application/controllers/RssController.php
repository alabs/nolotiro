<?php

class RssController extends Zend_Controller_Action {

    public function init() {

        $this->lang = $this->view->lang = $this->_helper->checklang->check(); 
    
    }

    public function feedAction() {

        $this->_helper->viewRenderer->setNoRender();
        $this->_helper->layout->disableLayout();

        $woeid = $this->_request->getParam('woeid');
        $ad_type = $this->_request->getParam('ad_type');

        $modelAd = new Model_Ad();
        $this->ads = $modelAd->getAdList($woeid, $ad_type, 25);

        $rss['title'] = 'nolotiro.org - '. $this->_helper->woeid->name($woeid, $this->lang).'/'. $ad_type;
        $rss['link'] = 'http://' . $_SERVER['HTTP_HOST'] .'/'.$this->lang.'/rss/feed/woeid/'.$woeid.'/ad_type/'.$ad_type;
        $rss['charset'] = 'utf-8';
        $rss['description'] = 'nolotiro.org - '. $this->_helper->woeid->name($woeid, $this->lang);
        $rss['language'] = $this->lang;
        $rss['generator'] = 'nolotiro.org';
        //$rss['published']  = date(DATE_RFC822);

        $rss['entries'] = array();

        foreach ($this->ads as $value) {

            $entry = array();
            $entry['title'] = $value['title'];
            $entry['link'] = 'http://' . $_SERVER['HTTP_HOST'] .'/'.$this->lang.'/ad/show/id/'.$value['id'].'/'.$value['title'];

            $entry['description'] = $value['body'];
            $entry['pubDate'] = date( DATE_RSS ,  strtotime($value['date_created']) );
            $rss['entries'][]  = $entry;

            
        }

        $feedObj = Zend_Feed::importArray($rss, 'rss');

        return $feedObj->send();

    }

}