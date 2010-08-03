<?php

class Zend_View_Helper_WoeidName  {


         function woeidName($woeid, $lang){

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
                            'logging' => false,
                            'write_control' => true,
                            'automatic_serialization' => true,
                            'ignore_user_abort' => true
                    ) );

            // build a caching object
            $cache = Zend_Cache::factory( $oFrontend, $oBackend );

             //locationtemp normalize spaces and characters not allowed (ñ) by memcached to create the item name
            $woeidHash = md5($woeid );

            $cachetest = $cache->test($woeidHash.$lang);

            if ($cachetest == false ){
                        $appid = ('bqqsQazIkY0X4bnv8F9By.m8ZpodvOu6');
                        $htmlString = 'http://where.yahooapis.com/v1/place/'.$woeid.'?appid='.$appid.'&lang='.$lang;

                        $name = simplexml_load_file($htmlString);
                        $name = $name->name.', '.$name->admin1.', '.$name->country;

                        $cache->save($name, $woeidHash.$lang);
                        //$name .= ' *no cached!';

                        } else {

                        $name = $cache->load($woeidHash.$lang);
                        //$name .= ' *cached!';
                        }
            
           return $name;
               
        }



       

}

