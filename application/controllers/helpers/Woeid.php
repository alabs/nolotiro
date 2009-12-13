<?php
/**
 * Description of Woeid
 *
 * @abstract this helper uses the Yahoo Gep Api, as helper could be used with any controller
 * @author dani remeseiro
 */
class Zend_Controller_Action_Helper_Woeid extends Zend_Controller_Action_Helper_Abstract {


         function name($woeid, $lang){

             //lets use memcached to not waste yahoo geo api requests
            
            // configure caching backend strategy
            $oBackend = new Zend_Cache_Backend_Memcached(
                    array(
                            'servers' => array( array(
                                    'host' => '127.0.0.1',
                                    'port' => '11211'
                            ) ),
                            'compression' => true
            ) );

            // configure caching frontend strategy
            $oFrontend = new Zend_Cache_Core(
                    array(
                            'caching' => true,
                            'cache_id_prefix' => 'woeidName',
                            'logging' => FALSE,
                            'write_control' => true,
                            'automatic_serialization' => true,
                            'ignore_user_abort' => true
                    ) );

            // build a caching object
            $cache = Zend_Cache::factory( $oFrontend, $oBackend );

            
            
            if (!$cache->test($woeid.$lang) ){
                        $appid = ('bqqsQazIkY0X4bnv8F9By.m8ZpodvOu6');
                        $htmlString = 'http://where.yahooapis.com/v1/place/'.$woeid.'?appid='.$appid.'&lang='.$lang;

                        $name = simplexml_load_file($htmlString);
                        $name = $name->name.', '.$name->admin1.', '.$name->country;

                        $cache->save($name, $woeid.$lang);
                        //$name .= ' *no cached!';

                        } else {

                        $name = $cache->load($woeid.$lang);
                        //$name .= ' *cached!';


                }
            
           return $name;
               
        }



       

}

