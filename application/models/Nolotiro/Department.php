<?php

/**
 * Zets_Department - a model class representing a single department. 
 * 
 * For now departments are fetched from a read-only XML file
 */

require_once 'application/models/Nolotiro/Employee.php';
require_once 'Zend/Registry.php';
require_once 'library/Nolotiro/Exception.php';
require_once 'www/index.php';

class Zets_Department
{
    protected $name = null;
    protected $children = null;
    
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

    /**
     * Get the department's children departments (Will return an array of 
     * Zets_Department objects or an empty array if none)
     *
     * @return array
     */
    public function getChildDepts()
    {
        if (!$this->name)
            throw new Zets_Exception('Cannot fetch children of an unknown department');
        
        if (!self::$xmlCache)
            self::loadXmlCache();
        
        $depts = self::$xmlCache->xpath("//department[name='{$this->name}']/department");
        
        $children = array ();
        if (is_array($depts))
            foreach ($depts as $d) {
                $dep = new Zets_Department();
                $dep->setName((string) $d->name);
                $children[] = $dep;
            }
        
        return $children;
    }

    /**
     * Get list of the employees of this department
     * 
     * @return array of Zets_Employee objects
     */
    public function getEmployees()
    {
        if (!$this->name)
            throw new Zets_Exception('Cannot fetch children of an unknown department');
        
        return Zets_Employee::getEmployeesByDepartment($this->name);
    }

    /**
     * Set the department's name
     *
     * @param  string $name
     * @return Zets_Department
     */
    public function setName($name)
    {
        $this->name = $name;
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
     * Return an array of all departments as Zend_Department objects. 
     * Each department will already be populated with it's children departments.
     *
     * @return array
     */
    public static function getDepartmentTreeRoot()
    {
        if (!self::$xmlCache)
            self::loadXmlCache();
        
        $departments = self::$xmlCache->xpath('/company/department');
        
        $root = array ();
        foreach ($departments as $d) {
            $dep = new Zets_Department();
            $dep->setName((string) $d->name);
            $root[] = $dep;
        }
        
        return $root;
    }

    /**
     * Get a single department object by it's name
     * 
     * @param  string $name
     * @return Zets_Department or null if not found
     */
    public static function getDepartmentByName($name)
    {
        if (!self::$xmlCache)
            self::loadXmlCache();
        
        $dept = self::$xmlCache->xpath("//department[name='{$name}']");
        if (empty($dept))
            return null;
        
        $d = new Zets_Department();
        $d->setName((string) $dept[0]->name);
        
        return $d;
    }
}