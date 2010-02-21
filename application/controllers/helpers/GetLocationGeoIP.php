<?php
/**
 * Description of Woeid
 *
 * @abstract this helper uses the GeoIP Api
 * @author dani remeseiro
 */
class Zend_Controller_Action_Helper_GetLocationGeoIP extends Zend_Controller_Action_Helper_Abstract {


         function suggest(){

		require_once ( NOLOTIRO_PATH_ROOT . '/library/GeoIP/geoipcity.inc' );

                //get the ip of the ad publisher
//                if (getenv(HTTP_X_FORWARDED_FOR)) {
//                    $ip = getenv(HTTP_X_FORWARDED_FOR);
//                } else {
//                    $ip = getenv(REMOTE_ADDR);
//                }

                $ip = '77.228.26.220';

		$gi = geoip_open("/usr/local/share/GeoIP/GeoLiteCity.dat",GEOIP_STANDARD);
	        $record = geoip_record_by_addr($gi,$ip);
	        
	        //$result .= $record->region . " " . $GEOIP_REGION_NAME[$record->country_code][$record->region] . "\n";
	        $result = $record->city.', ' ;
                $result .= $GEOIP_REGION_NAME[$record->country_code][$record->region].', ';
                $result .= $record->country_name;

		//var_dump($result);

		geoip_close($gi);

                return $result;


	}

       

}

