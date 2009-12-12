<?php
/**
 * Description of Woeid
 *
 * @author dani
 */
class Zend_Controller_Action_Helper_Woeid extends Zend_Controller_Action_Helper_Abstract {

        function direct($woeid){

            return 'kk';
        }


         function name($woeid, $lang){

            $appid = ('bqqsQazIkY0X4bnv8F9By.m8ZpodvOu6');
		$htmlString = 'http://where.yahooapis.com/v1/place/'.$woeid.'?appid='.$appid.'&lang='.$lang;

		$name = simplexml_load_file($htmlString);
		$name = $name->name.', '.$name->admin1.', '.$name->country;
               return $name;
        }



       

}

