<?php
/**
 * Description of Zend_View_Helper_SlugTitle
 *
 * @author Antonio Pardo
 */
 
class Zend_View_Helper_SlugTitle
{
    public function slugTitle($title)
    {
        $slug = strtolower(trim($title));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $this->transliterate($slug));

        return $slug;
    }

    private function transliterate($string) {
        return iconv("utf-8", "us-ascii//TRANSLIT", $string);
    }
}
