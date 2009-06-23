<?php

/**
 * Nolotiro_Page - a model class representing a page (editable, updatable by admins & editors)
 *
 * This is the DbTable class for the pages table.
 *  
 */
class Model_DbTable_Pages extends Zend_Db_Table {
	protected $_name = 'page';
	public function getPage($id) {
		$id = ( int ) $id;
		$row = $this->fetchRow ( 'id = ' . $id );
		if (! $row) {
			throw new Exception ( "Count not find row $id" );
		}
		return $row->toArray ();
	}
	public function addPage($body, $title) {
		$data = array ('body' => $body, 'title' => $title );
		$this->insert ( $data );
	}
	function updatePage($id, $body, $title) {
		$data = array ('body' => $body, 'title' => $title );
		$this->update ( $data, 'id = ' . ( int ) $id );
	}
	function deletePage($id) {
		$this->delete ( 'id =' . ( int ) $id );
	}
}
