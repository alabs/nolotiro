<?php
/**
 * Model_User - a model class representing a single user
 *
 * This is the DbTable class for the users table.
 *
 * */
class Model_User {


    /**
     * Save a new entry
     * @param  array $data
     * @return int|string
     */
    public function save(array $data) {
        $table = new Zend_Db_Table('users');
        $fields = $table->info ( Zend_Db_Table_Abstract::COLS );
        foreach ( $data as $field => $value ) {
            if (! in_array ( $field, $fields )) {
                unset ( $data [$field] );
            }
        }
        return $table->insert ( $data );
    }

    public function update(array $data) {
        $table = new Zend_Db_Table('users');
        $where = $table->getAdapter ()->quoteInto ( 'id= ?', (int)$data ['id'] );
        $table->update ( $data, $where );

    }

    public function checkEmail($email) {
        $table = new Zend_Db_Table('users');
        $select = $table->select ()->where ( 'email = ?', $email );
        return $table->fetchRow ( $select );
    }

    public function checkUsername($username) {
        $table = new Zend_Db_Table('users');
        $select = $table->select ()->where ( 'username = ?', $username );
        return $table->fetchRow ( $select );
    }

    public function getToken($email) {
        $table = new Zend_Db_Table('users');
        $select = $table->select ()->where ( 'email = ?', $email );
        return $table->fetchRow ( $select )->token;
    }

    public function validateToken($token) {
        $table = new Zend_Db_Table('users');
        $select = $table->select ()->where ( 'token = ?', $token );
        return $table->fetchRow ( $select );
    }

    /*
    public function checkIsLocked($id) {
        $table = new Zend_Db_Table('users');
        $select = $table->select ()->where ( 'id = ?', (int) $id );
        return $table->fetchRow ( $select )->locked;
    }*/

    public function checkWoeidUser($id) {
        $table = new Zend_Db_Table('users');
        $select = $table->select ('woeid')->where ( 'id = ?', (int) $id );
        return $table->fetchRow ( $select )->woeid;
    }

    public function checkLockedUser($id) {
        $table = new Zend_Db_Table('users');
        $select = $table->select ('locked')->where ( 'id = ?', (int) $id );
        $result = $table->fetchRow($select);
        if ($result)
            return $result->locked;
        else
            return null;
    }


    /**
     * Get an individual user by its id
     *
     * @param  int $id
     * @return null|Zend_Db_Table_Row_Abstract
     */
    public function fetchUser($id) {
        if (!$id)
            return null;
        $table = new Zend_Db_Table('users');
        $select = $table->select ()->where ( 'id = ?', $id );

        return $table->fetchRow ( $select );
    }

     public function fetchUserByUsername($username)
    {
        $table = new Zend_Db_Table('users');
        return $table->fetchRow(  $table->select()->where( 'username = ?', $username ) );
    }



    public function deleteUser($id) {
        $table = new Zend_Db_Table('users');
        $table->delete('id =' . (int)$id);
    }


    //friends area
    public function isMyFriend( $id_user, $id_friend ){
        $table = new Zend_Db_Table('friends');
        $select = $table->select()
                        ->where ( 'id_user = ?', $id_user )
                        ->where ( 'id_friend = ?', $id_friend );
        return $table->fetchRow ( $select );

    }



    public function fetchUserFriends($id){
        $id = (int)$id;
        $table = new Zend_Db_Table('friends');
        $select = $table->select()->setIntegrityCheck(false);

        $select->from(array('f' => 'friends'), array('f.id_friend'));
        $select->where ('f.id_user = ?', $id);
        $select->joinInner(array('u' => 'users'), 'f.id_friend = u.id', array('u.username'));

        return $table->fetchAll($select)->toArray();

    }


    public function addUserFriend($id_user, $id_friend){

        $id_user = (int)$id_user;
        $id_friend = (int)$id_friend;

        $table = new Zend_Db_Table('friends');
        $sql = "INSERT INTO friends   ( id_user, id_friend ) VALUES  ( $id_user , $id_friend )
                            ON DUPLICATE KEY UPDATE  id_user=id_user";
        return $table->getAdapter()->query($sql)->fetch();

    }


    public function deleteUserFriend($id_user, $id_friend){

        $id_user = (int)$id_user;
        $id_friend = (int)$id_friend;

        $db = Zend_Db_Table::getDefaultAdapter();

        $where = array();
        $where[] = $db->quoteInto('id_user = ?', $id_user);
        $where[] = $db->quoteInto('id_friend = ?', $id_friend);


        $db->delete('friends',$where);

    }


}
