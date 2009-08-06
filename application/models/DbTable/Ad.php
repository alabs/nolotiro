<?php

/**
 * Nolotiro_Ad - a model class representing an ad 
 *
 * This is the DbTable class for the ads table.
 *  
 */
class Model_DbTable_Ad extends Zend_Db_Table {
	protected $_name = 'ads';
	
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
}
