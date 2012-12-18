<?php

class Zend_View_Helper_WoeidName {

    function woeidName($woeid, $lang) {

        $oBackend = new Zend_Cache_Backend_Memcached(
                        array(
                            'servers' => array(array(
                                    'host' => '127.0.0.1',
                                    'port' => '11211'
                                )),
                            'compression' => true
                        ));

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

        //locationtemp normalize spaces and characters not allowed (ñ) by memcached to create the item name
        $woeidHash = md5($woeid);

        $cachetest = $cache->test($woeidHash . $lang);

        if ($cachetest == false) {
            $htmlString = "http://query.yahooapis.com/v1/public/yql?q=select%20name%2Cadmin1%2Ccountry%20from%20geo.places%20where%20woeid%3D$woeid%20and%20lang%3D%22$lang%22";

            $name = simplexml_load_file($htmlString);
            $name = get_object_vars($name->results->place);
            $name = $name[name] . ', ' . $name[admin1] . ', ' . $name[country];

            $cache->save($name, $woeidHash . $lang);


            //make sure we are going to store not null or empty
            if($name != null || !empty($name)){
                $cache->save($name, $woeidHash . $lang);
            }



        } else {
            $name = $cache->load($woeidHash . $lang);
        }

        return $name;
    }

}