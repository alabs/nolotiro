<?php
/**
 * Convenience class to build friendly dates
 *
 * @author David Rodríguez
 */

class Zend_View_Helper_BetterDate extends Zend_View_Helper_Abstract {

    public function betterDate($date) {

        $timestamp = strtotime($date);
        if ($timestamp >= strtotime('today'))
            $date = ucfirst($this->view->translate('today')) .
                    strftime(", %H:%M", $timestamp);
        else if ($timestamp >= strtotime('yesterday'))
            $date = ucfirst($this->view->translate('yesterday')) .
                    strftime(", %H:%M", $timestamp);
        else
            $date = strftime("%A %e %b, %H:%M", $timestamp);
        return $date;
    }
}
