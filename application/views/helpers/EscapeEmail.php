<?php
/**
 * Description of Zend_View_Helper_EscapeEmail
 *
 * @author dani remeseiro
 */
class Zend_View_Helper_EscapeEmail{


    public function escapeEmail($string){

                    //replace email by nothing
                    $string = preg_replace("/([\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+)/",  ' '  , $string);

        return $string;
    }


}

