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


    public function updateAd(array $data, $id){

        $table = new Model_Ad ();
		$fields = $table->info ( Zend_Db_Table_Abstract::COLS );
		foreach ( $data as $field => $value ) {
			if (! in_array ( $field, $fields )) {
				unset ( $data [$field] );
			}
		}
		return $table->update($data,  'id = ' . ( int ) $id);


    }



    public  function deleteAd($id) {
		$this->delete ( 'id =' . ( int ) $id );
	}




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

public function getAdforSearch($id, $ad_type) {
		$id = ( int )$id;
                $ad_type = (int)$ad_type;


		$table = new Model_Ad ( );
		$select = $table->select()->setIntegrityCheck(false);
		$select->from(array('a' => 'ads'), array('a.*' ));
		$select->joinInner(array('u' => 'users'), 'a.user_owner = u.id' , array('u.username'));
                $select->joinLeft(array('c' => 'commentsAdCount'), 'a.id = c.id_comment' , array('c.count as comments_count'));
                $select->joinLeft(array('r' => 'readedAdCount'), 'a.id = r.id_ad' , array('r.counter as readings_count'));

		$select->where ( 'a.id = ?', $id );
                $select->where ( 'a.type = ?', $ad_type );

                //show only if user is active and not blocked
                $select->where('u.active = ?', 1);
                $select->where('u.locked = ?', 0);

		if (!$table->fetchRow ( $select )) {
			$result =  null;

		} else {

		    $result = $table->fetchRow ( $select )->toArray();

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
	public function getAdList($woeid,$ad_type, $limit=NULL ) {
		$woeid = ( int ) $woeid;
		$ad_type = ( string ) $ad_type;
                

                if ($ad_type === 'give') {
                    $ad_type = 1;
                }

                if ($ad_type === 'want') {
                    $ad_type = 2;
                }
		
		$table = new Model_Ad ( );
		$select = $table->select()->setIntegrityCheck(false);
		$select->from(array('a' => 'ads'), array('a.*' ));
                $select->joinLeft(array('c' => 'commentsAdCount'), 'a.id = c.id_comment' , array('c.count as comments_count'));
                $select->joinLeft(array('r' => 'readedAdCount'), 'a.id = r.id_ad' , array('r.counter as readings_count'));
		$select->join(array('u' => 'users'), 'a.user_owner = u.id' , array('u.username'));

		
                //show only if user is active and not blocked
                $select->where('u.active = ?', 1);
                $select->where('u.locked = ?', 0);

		$select->where('a.woeid_code = ?', $woeid);
		$select->where('a.type = ?', $ad_type);
                //dont list not available items
                $select->where('a.status != ?', 'delivered');

                if($limit != NULL){
                    $select->limit( (int)$limit );
                }
		
		$select->order('a.date_created DESC');		
		
		$result = $table->fetchAll ( $select )->toArray ();

		
		return $result;
		
	}


        public function getAdListAll($ad_type ) {

                if ($ad_type === 'give') {
                    $ad_type = 1;
                }

                if ($ad_type === 'want') {
                    $ad_type = 2;
                }

		$table = new Model_Ad ( );
		$select = $table->select()->setIntegrityCheck(false);
		$select->from(array('a' => 'ads'), array('a.*' ));
                $select->joinLeft(array('c' => 'commentsAdCount'), 'a.id = c.id_comment' , array('c.count as comments_count'));
                $select->joinLeft(array('r' => 'readedAdCount'), 'a.id = r.id_ad' , array('r.counter as readings_count'));

                //fetch woeid name
                //TODO just testing, too slow to add in prod
                //$select->joinLeft(array('p' => 'geoplanet_places'), 'a.woeid_code = p.woeid' , array('p.name as geo_name'));

		$select->join(array('u' => 'users'), 'a.user_owner = u.id' , array('u.username'));
                //show only if user is active and not blocked
                $select->where('u.active = ?', 1);
                $select->where('u.locked = ?', 0);
                $select->where('a.type = ?', $ad_type);

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
                    $query = "SELECT ads.id as ad_id,ads.title,ads.body, ads.type,ads.date_created, ads.status, users.username,users.id   FROM ads,users
                        WHERE ads.user_owner = ".$id." AND users.id = ".$id." ORDER BY date_created DESC" ;
                   return $result = $ads_user->getAdapter()->query($query)->fetchAll();

                   

        }
	

        public function countReadedAd($id){

                if ($id){
                    $table = new Zend_Db_Table('readedAdCount');
                    $id = (int) $id;
                    $sql = "SELECT  counter FROM readedAdCount WHERE id_ad =  $id";
                    $result = $table->getAdapter()->query($sql)->fetch();

                    }

		return  $result;
        }



        public function updateReadedAd( $id ){
            
                $table = new Zend_Db_Table('readedAdCount');
                $id = (int) $id;                
                $sql = "INSERT INTO readedAdCount   ( id_ad, counter ) VALUES  ( $id , 1 )
                            ON DUPLICATE KEY UPDATE  counter=counter+1";
                $result = $table->getAdapter()->query($sql)->fetch();
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



