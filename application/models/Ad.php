<?php
/**
 * Nolotiro_Page - a model class representing ad crud
 *
 * This is the DbTable class for the ads table.
 *  
 */

class Model_Ad 

{
	/** Model_Table_Page */
	protected $_table;
	
	/**
	 * Retrieve table object
	 * 
	 * @return Model_User_Table
	 */
	public function getTable() {
		if (null === $this->_table) {
			// since the dbTable is not a library item but an application item,
			// we must require it to use it
			require_once APPLICATION_PATH . '/models/DbTable/Ad.php';
			$this->_table = new Model_DbTable_Ad ( );
		}
		return $this->_table;
	}
	
	/**
	 * Save a new entry
	 * 
	 * @param  array $data 
	 * @return int|string
	 */
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
	
	/**
	 * Fetch an individual entry
	 * 
	 * @param  int|string $id 
	 * @return null|Zend_Db_Table_Row_Abstract
	 */
	public function getAd($id) {
		$id = ( int ) $id;
		
		$table = $this->getTable ();
		$select = $table->select ()->where ( 'id = ?', $id );
		
		if (!$table->fetchRow ( $select )) {
			throw new Exception ( "Count not find row $id" );
			
		} else {
			$result = $table->fetchRow ( $select )->toArray ();
		}
		
		return $result;
		
	}

}



