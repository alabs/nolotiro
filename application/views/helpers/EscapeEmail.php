<?php
/**
 * Description of Zend_View_Helper_EscapeEmail
 *
 * @author dani
 */
class Zend_View_Helper_EscapeEmail{


    public function escapeEmail($string){

                    //convert txt emails to images
                    $imgStart = '<img src="/txttoimage.php?value=';
                    $imgEnd = '" />';
                    $string = preg_replace("/([\._a-zA-Z0-9-]+@[\._a-zA-Z0-9-]+)/", $imgStart.'xxx'. '$0'.'xxx'.$imgEnd , $string);

        return $string;
    }


}

