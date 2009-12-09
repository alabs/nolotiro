<?php

/**
 * Nolotiro_Ad - a model class representing an ad 
 *
 * This is the DbTable class for the ads table.
 *  
 */
class Model_Ad extends Zend_Db_Table_Abstract  {
	protected $_name = 'ads';
	protected $_primary = "id";
	protected $_dependentTables = array('comments');

    
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
		$table = new Model_Ad ();
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
		
		$table = new Model_Ad ( );
		$select = $table->select()->setIntegrityCheck(false);
		$select->from(array('a' => 'ads'), array('a.*' ));
		$select->joinInner(array('u' => 'users'), 'a.user_owner = u.id' , array('u.username'));
		
		$select->where ( 'a.id = ?', $id );
		
		if (!$table->fetchRow ( $select )) {
			throw new Exception ( "I can not find row $id" ,404);
			
		} else {
		    
		    
		    $result = $table->fetchRow ( $select )->toArray ();
			
		}
		
		return $result;
		
	}
	
	/**
	 * Fetch the comments of an ad
	 * 
	 * @param  int|string $id 
	 * @return null|Zend_Db_Table_Row_Abstract
	 */
	public function getComments($id) {
		$id = ( int ) $id;
		
		$table = new Model_Ad ( );
		$select = $table->select ()->where ( 'id = ?', $id );
		
		$result = $table->fetchRow ( $select )->findDependentRowset('Comment')->toArray ();
		
		return $result;
		
	}

	
	/**
	 * Fetch a list of ads where woeid and ad_type matches 
	 * 
	 * @param  int $woeid
	 * @param  string $ad_type  
	 * @return array list of ads with this params
	 */
	public function getAdList($woeid,$ad_type) {
		$woeid = ( int ) $woeid;
		$ad_type = ( string ) $ad_type;
		
		
		$table = new Model_Ad ( );
		$select = $table->select()->setIntegrityCheck(false);
		$select->from(array('a' => 'ads'), array('a.*' ));
		$select->joinInner(array('u' => 'users'), 'a.user_owner = u.id' , array('u.username'));
		$select->where('a.woeid_code = ?', $woeid);
		$select->where('a.type = ?', $ad_type);
		
		$select->order('a.date_created DESC');		
		
		$result = $table->fetchAll ( $select )->toArray ();

		
		return $result;
		
	}

	
	
}






class Comment extends Zend_Db_Table_Abstract  {
	protected $_name = 'comments';
    
	protected $_referenceMap    = array(
    'Ad' => array(
        'columns'           => array('ads_id'),
        'refTableClass'     => 'Model_Ad',
        'refColumns'        => array('id')
    )
);

	
	
	
	
	
}



