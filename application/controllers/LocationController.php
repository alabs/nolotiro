<?php
/**
 * @author Dani Remeseiro
 * @license http://www.gnu.org/licenses/agpl-3.0.html
 *
 */
class LocationController extends Zend_Controller_Action {

    public function init() {

        $this->lang = $this->view->lang = $this->_helper->checklang->check();
        $this->view->checkMessages = $this->_helper->checkMessages->check();

        $this->_flashMessenger = $this->_helper->getHelper('FlashMessenger');
        $this->view->mensajes = $this->_flashMessenger->getMessages();
    }

    public function indexAction() {
        $this->_redirect('/');
    }

    public function changeAction() {
        $request = $this->getRequest();
        $form = $this->_getLocationChangeForm();

        $this->view->page_title .= $this->view->translate('change location');

        $this->view->suggestIP = $this->_helper->getLocationGeoIP->suggest();

        if ($this->getRequest()->isPost()) {


            if ($form->isValid($request->getPost())) {

                $formulario = $form->getValues();
                //convert to lowercase and clean spaces
                $formulario['location'] = ucfirst(mb_convert_case(trim($formulario['location']), MB_CASE_LOWER, "UTF-8"));

                $aNamespace = new Zend_Session_Namespace('Nolotiro');
                $aNamespace->__set(locationTemp, $formulario['location']);

                $this->_redirect('/' . $this->view->lang . '/location/change2');
            }
        }

        // assign the form to the view
        $this->view->form = $form;
    }

    public function change2Action() {

        $request = $this->getRequest();
        $aNamespace = new Zend_Session_Namespace('Nolotiro');
        $locationtemp = $aNamespace->locationTemp;


        $this->view->page_title .= $this->view->translate('change location');
        //if is get overwrite the localtemp value
        if ($_GET['location']) {
            $locationtemp = $_GET['location'];
        }


        $places = $this->getYahooGeoWoeidList($locationtemp, $this->view->lang);


        //var_dump(get_object_vars($places));die;

        //check if we got response from yahoo geo api
        if ($places === false) {
            $this->_helper->_flashMessenger->addMessage(
                    $this->view->translate('I can not connect to Yahoo geo service, sorry!'));
            $this->_redirect('/' . $this->view->lang . '/woeid/' . $aNamespace->location . '/give');
        }

        //check if the yahoo geo api returns no results!
        if (count($places->place) == 0) {

            $this->_helper->_flashMessenger->addMessage(
                    $this->view->translate('No location found named:') . '  "' . $locationtemp . '"');
            $this->_redirect('/' . $this->view->lang . '/woeid/' . $aNamespace->location . '/give');
        }



        //if just one result then jump straight to change location
        if (count($places->place) == 1) {

            //if the user is logged then update the woeid value in ddbb, if not just update the session location value
            $auth = Zend_Auth::getInstance ();
            if ($auth->hasIdentity()) {

                require_once APPLICATION_PATH . '/models/User.php';
                $model = new Model_User();
                $data['id'] = $auth->getIdentity()->id;
                $data['woeid'] = (int) $places->place->woeid;
                $userUpdateLocation = $model->update($data);
            }

            $aNamespace = new Zend_Session_Namespace('Nolotiro');
            $aNamespace->location = (int) $places->place->woeid; //woeid
            setcookie('location', (int) $places->place->woeid, null, '/');

            $name = $places->place->name . ', ' . $places->place->admin1 . ', ' . $places->place->country;

            $aNamespace->locationName = $name; //location name

            $this->_helper->_flashMessenger->addMessage($this->view->translate('Location changed successfully to:') . ' ' . $name);
            $this->_redirect('/' . $this->view->lang . '/woeid/' . $places->place->woeid . '/give');
        }


        $form = $this->_getLocationChange2Form($locationtemp);
        // assign the form to the view
        $this->view->locationtemp = $locationtemp;
        $this->view->places = $places;
        $this->view->form = $form;

        $counter = 0;
        //*** here add the select values to the form from yahoo xml result
        foreach ($places->place as $item) {
            $name = $item->name . ', ' . $item->admin1 . ', ' . $item->country;
            $woeid = (string) $item->woeid; //we have to cast to string item to not disturb the zend form translate parser!
            //glue together woeid and text to parse after with *
            $woeid = $woeid . '*' . $name;

            $location_options[$woeid] = $name;

            //check the first value of the array results to show the first selected to form
            $counter++;
            if ($counter == 1) {
                $firstitem = $woeid;
            }
        }

        $form->addElement('select', 'location', array('validators'))
                ->getElement('location')
                ->addMultiOptions($location_options)
                ->setValue($firstitem)
                ->setRegisterInArrayValidator(false)
                ->setIsArray(true); //this set select expanded
        // add the submit button
        $form->addElement('submit', 'submit', array('label' => 'Choose your location'));


        // check to see if this action has been POST'ed to
        if ($this->getRequest()->isPost()) {

            if ($form->isValid($request->getPost())) {

                $formulario = $form->getValues();

                //parse the location value
                $values = explode("*", $formulario['location'][0]);

                //if the user is logged then update the woeid value in ddbb, if not just update the session location value
                $auth = Zend_Auth::getInstance ();
                if ($auth->hasIdentity()) {

                    require_once APPLICATION_PATH . '/models/User.php';
                    $model = new Model_User();
                    $data['id'] = $auth->getIdentity()->id;
                    $data['woeid'] = $values[0];
                    $userUpdateLocation = $model->update($data);
                }

                $aNamespace = new Zend_Session_Namespace('Nolotiro');
                $aNamespace->location = $values[0]; //woeid
                setcookie('location', $values[0], null, '/');

                $name = $item->name . ', ' . $item->admin1 . ', ' . $item->country;

                $aNamespace->locationName = $values[1]; //location name

                $this->_helper->_flashMessenger->addMessage($this->view->translate('Location changed successfully to:')
                        . ' ' . $values[1]);


                $this->_redirect('/' . $this->view->lang . '/woeid/' . $values[0] . '/give');
            }
        }
    }

    public function getYahooGeoWoeidList($locationtemp, $lang) {

        //lets use memcached to not waste yahoo geo api requests
        // configure caching backend strategy
        $oBackend = new Zend_Cache_Backend_Memcached(
                        array(
                            'servers' => array(array(
                                    'host' => '127.0.0.1',
                                    'port' => '11211'
                            )),
                            'compression' => true
                ));

        // configure caching frontend strategy
        $oFrontend = new Zend_Cache_Core(
                        array(
                            // cache for 7 days
                            'lifetime' => 3600 * 24 * 7,
                            'caching' => true,
                            'cache_id_prefix' => 'woeidList',
                            'logging' => FALSE,
                            'write_control' => true,
                            'automatic_serialization' => true,
                            'ignore_user_abort' => true
                ));

        // build a caching object
        $cache = Zend_Cache::factory($oFrontend, $oBackend);

        //locationtemp normalize spaces and characters not allowed (ñ) by memcached to create the item name
        $locationtempHash = md5($locationtemp);

        $cachetest = $cache->test('Loc' . $locationtempHash . $lang);



        if ($cachetest) {
            $xml = $this->_unserializemmp($cache->load('Loc' . $locationtempHash . $lang));

        } else {
            $htmlString = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22".
                urlencode($locationtemp). "%22%20and%20lang%3D%22$lang%22";

            $xml = simplexml_load_file($htmlString);
            $xml = $xml->results;

            $cache->save($this->_serializemmp($xml), 'Loc' . $locationtempHash . $lang);
        }

        return (object)$xml;
    }

    //*************************************************************
    protected function _serializemmp($toserialize) {
        if (is_a($toserialize, "SimpleXMLElement")) {
            $stdClass = new stdClass();
            $stdClass->type = get_class($toserialize);
            $stdClass->data = $toserialize->asXml();
        }
        return serialize($stdClass);
    }

    protected function _unserializemmp($tounserialize) {
        $tounserialize = unserialize($tounserialize);
        if (is_a($tounserialize, "stdClass")) {
            if ($tounserialize->type == "SimpleXMLElement") {
                $tounserialize = simplexml_load_string($tounserialize->data);
            }
        }
        return $tounserialize;
    }

    /**
     *
     * @return Form_LocationChange
     */
    protected function _getLocationChangeForm() {
        require_once APPLICATION_PATH . '/forms/LocationChange.php';
        $form = new Form_LocationChange();
        return $form;
    }

    protected function _getLocationChange2Form($locationtemp) {
        require_once APPLICATION_PATH . '/forms/LocationChange2.php';
        $form = new Form_LocationChange2();
        return $form;
    }

}

