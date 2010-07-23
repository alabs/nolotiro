<?php
/**
 * Description of Woeid
 *
 * @abstract this helper uses the GeoIP Api
 * @author dani remeseiro
 */
class Zend_Controller_Action_Helper_GetLocationGeoIP extends Zend_Controller_Action_Helper_Abstract {


         function suggest(){

		require_once ( NOLOTIRO_PATH . '/library/GeoIP/geoipcity.inc' );

                if (getenv(HTTP_X_FORWARDED_FOR)) {
                    $ip = getenv(HTTP_X_FORWARDED_FOR);
                } else {
                    $ip = getenv(REMOTE_ADDR);
                }

                
                //$ip = '67.195.114.53';//yahoo slurp
                //$ip = '66.249.71.206';//google bot


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

