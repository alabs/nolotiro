<?php

class Zend_Controller_Action_Helper_SimilarLocations extends Zend_Controller_Action_Helper_Abstract
{

    public function suggest($location, $lang)
    {
        $location = explode(',', $location);

        //TODO get the proper lang from yahoo and placeTypeName = town
        $htmlString = "http://query.yahooapis.com/v1/public/yql?q=select%20*%20from%20geo.places%20where%20text%3D%22".
            urlencode($location[0]). "%22&lang=$lang";

        $objXml = simplexml_load_file($htmlString);
        $objXml = get_object_vars($objXml->results);

        $n = 0;
        foreach ($objXml['place'] as $loc) {
            $locations[$n]['name'] = (string)$loc->name;
            $locations[$n]['admin1'] = (string)$loc->admin1;
            $locations[$n]['country'] = (string)$loc->country;
            ++$n;
        }

        return $locations;
    }


}