<?php

class Zend_Controller_Action_Helper_Checklang extends Zend_Controller_Action_Helper_Abstract {


         function check(){


              $lang = $_COOKIE['lang'];


                if ( $lang == null){

                  $locale = new Zend_Locale ( );
                  $locale->setLocale ( 'en' );
                  setcookie ( 'lang', $locale->getLanguage (), null, '/' );
                  Zend_Registry::set ( 'Zend_Locale', $locale );

                  $lang = 'en';
                }


                $locale = Zend_Registry::get ( "Zend_Locale" );
                $lang = $locale->getLanguage ();

                return $lang;


        }



}

