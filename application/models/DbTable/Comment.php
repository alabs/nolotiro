<?php

/**
 * Nolotiro_User - a model class representing a single comment
 * This is the DbTable class for the comments table.
 */

class Model_DbTable_Comment extends Zend_Db_Table_Abstract
{
  protected $_name = 'comments';
  protected $_primary = 'id';
  protected $_referenceMap = array ( 'Ad' => array ('columns' => array('id'),
                                                    'refTableClass' => 'Ad',
                                                    'refColumns' => array('id')));

  /**
   * @abstract inserts a comment row
   * @return $id
   */
  public function insert(array $data) {
    //$data ['created'] = date ( 'Y-m-d H:i:s' );
    //$data ['token'] = md5 ( uniqid ( rand (), 1 ) );
    return parent::insert ( $data );
  }

}
