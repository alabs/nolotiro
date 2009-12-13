<?php
/**
 * LocationController
 *
 */

class LocationController extends Zend_Controller_Action {

	public function init() {
		parent::init ();
		$this->view->baseUrl = Zend_Controller_Front::getParam ( $route );

		$locale = Zend_Registry::get ( "Zend_Locale" );
		$this->lang = $locale->getLanguage ();

               
                
	}


        public function indexAction(){
             $this->_redirect ( '/' );
        }






	public function changeAction(){
		$request = $this->getRequest();
		$form = $this->_getLocationChangeForm();
		
		
		//get the ip of the ad publisher
                    if (getenv(HTTP_X_FORWARDED_FOR)) {
                        $ip = getenv(HTTP_X_FORWARDED_FOR);
                    } else {
                        $ip = getenv(REMOTE_ADDR);
                    }

		$this->getLocationGeoIP($ip);
		// check to see if this action has been POST'ed to
		if ($this->getRequest ()->isPost ()) {


			if ($form->isValid ( $request->getPost () )) {


				$formulario = $form->getValues ();


				$aNamespace = new Zend_Session_Namespace('Nolotiro');
				$aNamespace->locationTemp = $formulario['location'];

				

				$this->_redirect ( '/'.$this->lang.'/location/change2' );
				
				


			}
		}
		// assign the form to the view
		$this->view->form = $form;


	}

	public function change2Action(){

		$aNamespace = new Zend_Session_Namespace('Nolotiro');
		$locationtemp = $aNamespace->locationTemp;

		
		$places = $this->getYahooGeoWoeidList($locationtemp);

		
		//check if we got response from yahoo geo api
		if ($places === false) {
			$this->_helper->_flashMessenger->addMessage (
				$this->view->translate ( 'I can not connect to Yahoo geo service, sorry!'));
			$this->_redirect ( '/'.$this->lang.'/ad/list/woeid/'.$aNamespace->location.'/ad_type/give' );
			
		}

		//check if the yahoo geo api returns no results!
		if ( count($places->place ) == 0){
					
			$this->_helper->_flashMessenger->addMessage (
				$this->view->translate ( 'No location found named:') .'  "'. $locationtemp .'"');
			$this->_redirect ( '/'.$this->lang.'/ad/list/woeid/'.$aNamespace->location.'/ad_type/give' );
		
		}

		$request = $this->getRequest();
		$form = $this->_getLocationChange2Form($locationtemp);


		// assign the form to the view
		$this->view->locationtemp = $locationtemp;
		$this->view->places = $places;

		$this->view->form = $form;

		//*** here add the select values to the form from yahoo xml result

		foreach ($places as $item) {

			$name = $item->name.', '.$item->admin1.', '.$item->country;
	
			$woeid = (string)$item->woeid; //we have to cast to string item to not disturb the zend form translate parser!
	
			//glue together woeid and text to parse after with *
			$woeid = $woeid.'*'.$name;
	
			$location_options[$woeid]= $name;

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
		->setIsArray(true);//this set select expanded


		// add the submit button
		$form->addElement ( 'submit', 'submit', array ('label' => 'Choose your location' ) );


		// check to see if this action has been POST'ed to
		if ($this->getRequest ()->isPost ()) {


			if ($form->isValid ( $request->getPost () )) {


				$formulario = $form->getValues ();

				//parse the location value
				$values = explode("*", $formulario['location'][0]);


				$aNamespace = new Zend_Session_Namespace('Nolotiro');
				$aNamespace->location = $values[0];//woeid

				$name = $item->name.', '.$item->admin1.', '.$item->country;



				$aNamespace->locationName = $values[1];//location name


				$this->_helper->_flashMessenger->addMessage ( $this->view->translate ( 'Location changed successfully to:' )
				.' '.$values[1]);

				$this->_redirect ( '/'.$this->lang.'/ad/list/woeid/'.$values[0].'/ad_type/give' );

			}
		}



	}

	public function getYahooGeoWoeidList($locationtemp){

		$appid = ('bqqsQazIkY0X4bnv8F9By.m8ZpodvOu6');
		$htmlString = 'http://where.yahooapis.com/v1/places$and(.q('.
		urlencode($locationtemp).'),.type('.$this->view->translate ('Town').'));count=20?appid='.$appid.'&lang='.$this->lang;

		$xml = simplexml_load_file($htmlString);

		return $xml;

	}


        


	/**
	 *
	 * @return Form_LocationChange
	 */
	protected function _getLocationChangeForm() {
		require_once APPLICATION_PATH . '/forms/LocationChange.php';
		$form = new Form_LocationChange();
		//$form->setAction($this->_helper->url(''));
		return $form;
	}

	protected function _getLocationChange2Form($locationtemp) {
		require_once APPLICATION_PATH . '/forms/LocationChange2.php';
		$form = new Form_LocationChange2();
		//$form->setAction($this->_helper->url(''));
		return $form;
	}




	public function getLocationGeoIP($IP){

		require_once ( NOLOTIRO_PATH_ROOT . '/library/GeoIP/geoipcity.inc' );


		$gi = geoip_open("/usr/local/share/GeoIP/GeoLiteCity.dat",GEOIP_STANDARD);

	        $record = geoip_record_by_addr($gi,$IP);
	        print $record->country_name . "\n";
	        //print $record->region . " " . $GEOIP_REGION_NAME[$record->country_code][$record->region] . "\n";
	        print $GEOIP_REGION_NAME[$record->country_code][$record->region];
	        print $record->city . "\n";
	
		var_dump($record);

		geoip_close($gi);



	}


}


