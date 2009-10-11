<?php
/**
 * Nolotiro_User - a model class representing a single user
 * This is the DbTable class for the users table.
 *
 */

class Model_DbTable_User extends Zend_Db_Table_Abstract 

{
	protected $_name = 'users';
	protected $_primary = 'id';
	
	/**
	 * @abstract inserts a user row
	 * @return $id
	 */
	public function insert(array $data) {
		$data ['created'] = date ( 'Y-m-d H:i:s' );
		$data ['token'] = md5 ( uniqid ( rand (), 1 ) );
		
		return parent::insert ( $data );
	
	}

}