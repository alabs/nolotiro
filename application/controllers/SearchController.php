<?php

class SearchController extends Zend_Controller_Action{

    public function init() {
        
        $this->lang = $this->view->lang =  $this->_helper->checklang->check();
        $this->_flashMessenger = $this->_helper->getHelper ( 'FlashMessenger' );
        $this->view->mensajes = $this->_flashMessenger->getMessages ();


        require_once ( APPLICATION_PATH . '../../library/Sphinx/sphinxapi.php' );
        
        $this->tcount = 0;

        $this->cl = new SphinxClient();
        $this->cl->SetServer( '127.0.0.1', 3312);
        $this->cl->SetMatchMode(SPH_MATCH_EXTENDED2);
        $this->cl->SetRankingMode(SPH_RANK_PROXIMITY);
        $this->cl->SetFieldWeights(array('metadata' => 1, 'filename' => 10));
        $this->cl->SetSelect("*, sum(@weight*isources*sources/fnCount) as fileWeight");
        $this->cl->SetSortMode(SPH_SORT_EXTENDED, "@weight DESC, fnWeight DESC, isources DESC");
        $this->cl->SetGroupBy("idfile", SPH_GROUPBY_ATTR, "fileWeight DESC, isources DESC, fnCount DESC");
        $this->cl->SetMaxQueryTime(1000);
    }


    public function indexAction(){

        $qw = stripcslashes(strip_tags($this->_getParam('q')));

         // Create a filter chain and add filters
        $encoding = array('quotestyle' => ENT_QUOTES, 'charset' => 'UTF-8');

        $f = new Zend_Filter();
        //$f->addFilter(new Zend_Filter_HtmlEntities($encoding));
        $f->addFilter(new Zend_Filter_StringTrim());
        $f->addFilter(new Zend_Filter_StripTags($encoding));

        $q = $f->filter ( trim($qw ));

        $this->view->page_title .= $this->view->translate('search');
        $this->view->page_title .=  ' - ';
        $this->view->page_title .=  $q;

        $this->view->headTitle()->append($qw);

       
        $form = $this->_getSearchForm();
         if (!$q) { // check if query search is empty

            $this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Hey! Write something' ) );
            //$this->_redirect ( '/' );
            return ;
        }

        $form->getElement('q')->setValue(trim($q));
        $form->loadDefaultDecoratorsIsDisabled(false);
        
        //$form->addElement("hidden", "src", array("value"=>$src));
        

        foreach($form->getElements() as $element) {
            $element->removeDecorator('DtDdWrapper');
            $element->removeDecorator('Label');
        }

        // assign the form to the view
        $this->view->form = $form;




    }

    /**
         *
         * @return Form_Search
         */
        protected function _getSearchForm() {
                require_once APPLICATION_PATH . '/forms/Search.php';
                $form = new Form_Search( );
                return $form;
        }


}
