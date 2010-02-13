<?php
/**
 * Nolotiro_User - a model class representing a single user
 *
 * This is the DbTable class for the users table.
 *
 * */
class Model_User 

{
	/** Model_Table_User */
	protected $_table;
	
	/**
	 * Retrieve table object
	 * @return Model_User_Table
	 * */
	public function getTable() {
		if (null === $this->_table) {
			// since the dbTable is not a library item but an application item,
			// we must require it to use it
			require_once APPLICATION_PATH . '/models/DbTable/User.php';
			$this->_table = new Model_DbTable_User ( );
		}
		return $this->_table;
	}
	
	/**
	 *	 * Save a new entry
	 * * @param  array $data
	 * * @return int|string
	 * */
	public function save(array $data) {
		$table = $this->getTable ();
		$fields = $table->info ( Zend_Db_Table_Abstract::COLS );
		foreach ( $data as $field => $value ) {
			if (! in_array ( $field, $fields )) {
				unset ( $data [$field] );
			}
		}
		return $table->insert ( $data );
	}
	
	public function update(array $data) {
		$table = $this->getTable ();
		$where = $table->getAdapter ()->quoteInto ( 'id= ?', $data ['id'] );
		$table->update ( $data, $where );
	
	}
	
	public function checkEmail($email) {
		$table = $this->getTable ();
		$select = $table->select ()->where ( 'email = ?', $email );
		return $table->fetchRow ( $select );
	}
	
	public function checkUsername($username) {
		$table = $this->getTable ();
		$select = $table->select ()->where ( 'username = ?', $username );
		return $table->fetchRow ( $select );
	}
	
	public function getToken($email) {
		$table = $this->getTable ();
		$select = $table->select ()->where ( 'email = ?', $email );
		return $table->fetchRow ( $select )->token;
	}
	
	public function validateToken($token) {
		$table = $this->getTable ();
		$select = $table->select ()->where ( 'token = ?', $token );
		return $table->fetchRow ( $select );
	}

        public function checkIsLocked($id) {
		$table = $this->getTable ();
		$select = $table->select ()->where ( 'id = ?', $id );
		return $table->fetchRow ( $select )->locked;
	}
	
	/**
	 * Fetch an individual entry
	 * @param  int|string $id
	 * @return null|Zend_Db_Table_Row_Abstract
	 */
	public function fetchUser($id) {
		$table = $this->getTable ();
		$select = $table->select ()->where ( 'id = ?', $id );
		// see reasoning in fetchEntries() as to why we return only an array
		return $table->fetchRow ( $select )->toArray ();
	}

}
