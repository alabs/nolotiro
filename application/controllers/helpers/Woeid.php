<?php
/**
 * Description of Woeid
 *
 * @abstract this helper uses the Yahoo Gep Api, as helper could be used with any controller
 * @author dani remeseiro
 */
class Zend_Controller_Action_Helper_Woeid extends Zend_Controller_Action_Helper_Abstract {

    function name($woeid, $lang) {

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
                            'cache_id_prefix' => 'woeidName',
                            'logging' => false,
                            'write_control' => true,
                            'automatic_serialization' => true,
                            'ignore_user_abort' => true
                        ));

        // build a caching object
        $cache = Zend_Cache::factory($oFrontend, $oBackend);
        $woeidHash = md5($woeid);

        $cachetest = $cache->test($woeidHash . $lang);

        if ($cachetest == false) {
            //TODO get the lang $lang from yahoo api  and placeTypeName = town
            $htmlString = "http://query.yahooapis.com/v1/public/yql?q=select%20name%2Cadmin1%2Ccountry%20from%20geo.places%20where%20woeid%3D$woeid";

            $name = simplexml_load_file($htmlString);
            $name = get_object_vars($name->results->place);
            $name = $name[name] . ', ' . $name[admin1] . ', ' . $name[country];

            $cache->save($name, $woeidHash . $lang);

        } else {
            $name = $cache->load($woeidHash . $lang);
        }

        return $name;
    }

}