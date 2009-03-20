<?php

/**
 * Zets_Employee - a model class representing a single employee
 * 
 * For now data is fetched from a read-only XML file
 */

require_once 'Zend/Registry.php';
require_once 'library/Nolotiro/Exception.php';
require_once 'www/index.php';

class Zets_Employee
{
    protected $name = null;
    protected $email = null;
    protected $title = null;
    
    /**
     * Internal cache of the SimpleXML object containing the data
     *
     * @var SimpleXMLElement
     */
    protected static $xmlCache = null;

    /**
     * et the department's name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;
        return $this;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle($title)
    {
        $this->title = $title;
        return $this;
    }

    /**
     * Helper method to load the XML file into internal cache
     *
     */
    protected static function loadXmlCache()
    {
        $xmlfile = ZETS_PATH_ROOT . DIRECTORY_SEPARATOR . Zend_Registry::get('config')->data->file;
        
        if (!file_exists($xmlfile))
            throw new Zets_Exception('Unable to read data XML file: ' . $xmlfile);
        
        self::$xmlCache = simplexml_load_file($xmlfile);
    }

    /**
     * Get an array of department employees by the department name
     *
     * @param string $dept
     */
    public static function getEmployeesByDepartment($dept)
    {
        if (!self::$xmlCache)
            self::loadXmlCache();
        
        $deptemps = self::$xmlCache->xpath("//department[name='" . $dept . "']/employee");
        
        $emps = array ();
        foreach ($deptemps as $e) {
            $emp = new Zets_Employee();
            $emp->setName((string) $e->name);
            $emp->setTitle((string) $e->title);
            $emp->setEmail((string) $e->email);
            
            $emps[] = $emp;
        }
        
        return $emps;
    }
}