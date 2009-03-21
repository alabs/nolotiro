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
    public function insert(array $data)
    {
    	$data['created'] = date('Y-m-d H:i:s');
        return parent::insert($data);
    }

    public function update(array $data, $where)
    {
        throw new Exception('chst! no se puede updatear un user');
    }
    
}