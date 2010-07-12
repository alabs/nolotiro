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
	protected $_dependentTables = array('comments', 'users');

    
	/** Model_Table_Page */
	protected $_table;
	
	
	
	/**
	 * Save a new entry
	 * 
	 * @param  array $data 
	 * @return int|string
	 */
	public function createAd(array $data) {
		$table = new Model_Ad ();
		$fields = $table->info ( Zend_Db_Table_Abstract::COLS );
		foreach ( $data as $field => $value ) {
			if (! in_array ( $field, $fields )) {
				unset ( $data [$field] );
			}
		}
		return $table->insert ( $data );
	}




	public function updateAd( $id, $title, $body, $type, $status, $comments_enabled ) {
		$data = array ( 'title' => $title, 'body' => $body, 'type' => $type, 'status' => $status, 'comments_enabled' => $comments_enabled );
		$this->update ( $data, 'id = ' . ( int ) $id );

	}



	function deleteAd($id) {
		$this->delete ( 'id =' . ( int ) $id );
	}





	/**
	 * Fetch an individual entry
	 * 
	 * @param  int|string $id 
	 * @return array
	 */
	public function getAd($id) {
		$id = ( int )$id;
		
		$table = new Model_Ad ( );
		$select = $table->select()->setIntegrityCheck(false);
		$select->from(array('a' => 'ads'), array('a.*' ));
		$select->joinInner(array('u' => 'users'), 'a.user_owner = u.id' , array('u.username'));
		
		$select->where ( 'a.id = ?', $id );
		
		if (!$table->fetchRow ( $select )) {
			$result =  null;
			
		} else {
		    
		    $result = $table->fetchRow ( $select )->toArray ();
                    
		}
		
		return $result;
		
	}




	/**
	 * Fetch the comments of an ad
	 * 
	 * @param  int|string $id 
	 * @return  array
	 */
	public function getComments($id) {
                    $id = ( int ) $id;
                
                    if ($id){

                    $comments = new Zend_Db_Table('comments');
                    $query = "SELECT comments.* , users.username  FROM comments,users WHERE comments.ads_id = ".$id." AND comments.user_owner = users.id ";
                    $result = $comments->getAdapter()->query($query)->fetchAll();

                    }
                    
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
                //show only if user is active and not blocked
                $select->where('u.active = ?', 1);
                $select->where('u.locked = ?', 0);

		$select->where('a.woeid_code = ?', $woeid);
		$select->where('a.type = ?', $ad_type);
                //dont list not available items
                $select->where('a.status != ?', 'delivered');

		
		$select->order('a.date_created DESC');		
		
		$result = $table->fetchAll ( $select )->toArray ();

		
		return $result;
		
	}


        public function getAdListAll() {
		$table = new Model_Ad ( );
		$select = $table->select()->setIntegrityCheck(false);
		$select->from(array('a' => 'ads'), array('a.*' ));
		$select->joinInner(array('u' => 'users'), 'a.user_owner = u.id' , array('u.username'));
                //show only if user is active and not blocked
                $select->where('u.active = ?', 1);
                $select->where('u.locked = ?', 0);


                //dont list not available items
                //$select->where('a.status != ?', 'delivered');


		$select->order('a.date_created DESC');

		$result = $table->fetchAll ( $select )->toArray ();


		return $result;

	}


        /**
	 * Fetch a list of ads where id_owner user matches
	 *
	 * @param  int $woeid
	 * @return array
	 */
	public function getAdUserlist($id) {

                    $table = new Model_Ad ( );
                    $select = $table->select ()->where ( 'user_owner = ?', ( int )$id );
                    $select->order('date_created DESC');
                    $result = $table->fetchAll ( $select )->toArray ();


                    $ads_user = new Zend_Db_Table('ads');
                    $query = "SELECT ads.id as ad_id,ads.title,ads.body,ads.date_created, ads.status, users.username,users.id   FROM ads,users
                        WHERE ads.user_owner = ".$id." AND users.id = ".$id." ORDER BY date_created DESC" ;
                   return $result = $ads_user->getAdapter()->query($query)->fetchAll();

                   

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



