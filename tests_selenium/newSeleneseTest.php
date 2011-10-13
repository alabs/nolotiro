<?php

require_once 'PHPUnit/Extensions/SeleniumTestCase.php';

/**
 * Description of newSeleneseTest
 *
 * @author dani remeseiro
 */
class newSeleneseTest extends PHPUnit_Extensions_SeleniumTestCase
{

    protected function setUp()
    {

        $this->setBrowser('*custom /usr/lib/firefox-7.0.1/firefox -P Selenium');
        $this->setBrowserUrl("http://nolotiro.dev/");
    }

    public function testLoginUserDani6Case()
    {
        $this->start();
        $this->open("/es/ad/list/woeid/766273/ad_type/give");
        $this->click("link=acceder");
        $this->waitForPageToLoad("30000");
        $this->type("email", "daniel.remeseiro+test6@gmail.com");
        $this->type("password", "9t4wgq");
        $this->click("xpath=//dd[@id='submit-element']/input");
        $this->waitForPageToLoad("30000");

        $this->stop();
    }

}

