<?php

/**
 * Message - a model class representing a private message keeped on ddbb
 *
 * This is the DbTable class for the messages table.
 *  
 */
class Model_Message extends Zend_Db_Table_Abstract  {
	protected $_name = 'messages';
	protected $_primary = "id";
	

    
	/** Model_Table_Page */
	protected $_table;
	
	
	public function addAd($body, $title) {

		$data = array ('body' => $body, 'title' => $title );
		$this->insert ( $data );
	}
	function updateAd($id, $body, $title) {
		$data = array ('body' => $body, 'title' => $title );
		$this->update ( $data, 'id = ' . ( int ) $id );
	}
	function deleteAd($id) {
		$this->delete ( 'id =' . ( int ) $id );
	}

	
	/**
	 * Save a new entry
	 * 
	 * @param  array $data 
	 * @return int|string
	 */
	public function save(array $data) {
		$table = new Model_Message();
		$fields = $table->info ( Zend_Db_Table_Abstract::COLS );
		foreach ( $data as $field => $value ) {
			if (! in_array ( $field, $fields )) {
				unset ( $data [$field] );
			}
		}
		return $table->insert ( $data );
	}
	
	/**
	 * get email of user id
	 *
	 * @param  int|string $id
	 * @return  array
	 */
	public function getEmailUser($id) {
                    $id = ( int ) $id;

                    if ($id){
                        $comments = new Zend_Db_Table('users');
                        $query = "SELECT users.email  FROM users WHERE users.id = ".$id;
                        $result = $comments->getAdapter()->query($query)->fetchColumn();
                    }

		return $result;

	}
	
	

	
	
}






