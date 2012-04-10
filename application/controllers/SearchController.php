<?php
class SearchController extends Zend_Controller_Action {

    public function indexAction() {


        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->location = $this->_helper->checklocation->check();
        $this->view->checkMessages  = $this->_helper->checkMessages->check();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->mensajes = $this->_flashMessenger->getMessages();


        $this->view->ad_type = $ad_type = $this->_getParam('ad_type');
        $qw = stripcslashes(strip_tags($this->_getParam('q')));



        require_once ( APPLICATION_PATH . '../../library/Sphinx/sphinxapi.php' );
        $this->cl = new SphinxClient();
        $this->cl->SetServer('127.0.0.1', 3312);
        $this->cl->SetMatchMode(SPH_MATCH_EXTENDED2);
        $this->cl->SetRankingMode(SPH_RANK_PROXIMITY);


//        $this->cl->SetFieldWeights(array('metadata' => 1, 'filename' => 10));
        $this->cl->SetSortMode(SPH_SORT_EXTENDED, "@id DESC");
        $this->cl->SetMaxQueryTime(1000);
        //*************************************************************************************
        
        // Create a filter chain and add filters
        $encoding = array('quotestyle' => ENT_QUOTES, 'charset' => 'UTF-8');
        $f = new Zend_Filter();
        $f->addFilter(new Zend_Filter_StringTrim());
        $f->addFilter(new Zend_Filter_StripTags($encoding));
        $q = $this->view->q = $f->filter(trim($qw));

        $this->view->page_title .= $this->view->translate('search');
        $this->view->page_title .= ' - '. $q;

        $page = $this->_request->getParam('page');

        if ($page) {
            $this->view->page_title .= ' - '.$this->view->translate('page').' '.$page;
        }

        

        require_once APPLICATION_PATH . '/forms/Search.php';
        $form = new Form_Search( );


        if (!$q) { // check if query search is empty
            $this->_helper->_flashMessenger->addMessage($this->view->translate('Hey! Write something'));
            $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
            return;
        }

        $form->getElement('q')->setValue(trim($q));
     

        $form->loadDefaultDecoratorsIsDisabled(false);
        foreach ($form->getElements() as $element) {
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('Label');
        }
        $this->view->form = $form;


        ////*****************************************
        $this->cl->SetFilter('type', array($ad_type) );
        $this->cl->SetFilter('woeid_code', array($this->location) );
        $result = $this->cl->Query($q, 'ads');


        if ($result === false) {
            echo "Query failed: " . $this->cl->GetLastError() . ".\n";
        } else {
            if ($this->cl->GetLastWarning()) {
                echo "WARNING: " . $this->cl->GetLastWarning() . "";
            }

            $modelAd = new Model_Ad();
            
            if (!is_null($result["matches"])) {
                foreach ($result["matches"] as $doc => $docinfo) {

                    $resultzs[$doc] = $modelAd->getAdforSearch($doc, $ad_type, $this->location);
                   
                }
                 
                $this->view->query_time = $result['time'];
                $this->view->total_found = $result['total_found'];


                $paginator = Zend_Paginator::factory($resultzs);
                $paginator->setDefaultScrollingStyle('Elastic');
                $paginator->setItemCountPerPage(20);
                $paginator->setCurrentPageNumber($page);

                $this->view->search = $paginator;
            } else {
                $this->_helper->_flashMessenger->addMessage($this->view->translate('Sorry, no results for search:') . ' <b>"' . $q . '"</b>');
                $this->_redirect('/' . $this->lang . '/woeid/' . $this->location . '/give');
            }
        }
    }



}
