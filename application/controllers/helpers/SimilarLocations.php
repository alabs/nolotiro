<?php

class Zend_Controller_Action_Helper_SimilarLocations extends Zend_Controller_Action_Helper_Abstract
{


    public function suggest($location, $lang)
    {


        $location = explode(',', $location);

        $appid = ('bqqsQazIkY0X4bnv8F9By.m8ZpodvOu6');
        $htmlString = "http://where.yahooapis.com/v1/places\$and(.q(" .
            urlencode($location[0]) . "),.type(Town));count=30?appid=" . $appid . "&lang=" . $lang;


        $objXml = simplexml_load_file($htmlString);

        $n = 0;
        foreach ($objXml as $loc) {
            $locations[$n]['name'] = (string)$loc->name;
            $locations[$n]['admin1'] = (string)$loc->admin1;
            $locations[$n]['country'] = (string)$loc->country;
            ++$n;
        }

        //var_dump($locations);

        return $locations;
    }


}