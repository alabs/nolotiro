<?php

/**
 * Nolotiro_User - a model class representing a single user
 *
 * This is the DbTable class for the users table.
 *  
 */



class Model_DbTable_User extends Zend_Db_Table_Abstract

{
    protected $_name = 'users';
      
    
	    
    
    /**
     * @abstract get a users nameeeeeeeeeeeeee rowww
     *
     * @return $id
     */
    public function getName(int $id)
    {
        return parent::fetchRow($id);
    }

    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
}