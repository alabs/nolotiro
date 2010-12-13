<?php

class Model_Message extends Zend_Db_Table_Abstract {

    protected $_name = 'messages';
    protected $_primary = "id";
    /** Model_Table_Page */
    protected $_table;

    /**
     * Save a new message
     *
     * @param  array $data
     * @return int|string
     */
    public function save(array $data) {
        $table = new Model_Message();
        $fields = $table->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $field => $value) {
            if (!in_array($field, $fields)) {
                unset($data [$field]);
            }
        }
        return $table->insert($data);
    }

    /**
     * get email of user id
     *
     * @param  int|string $id
     * @return  array
     */
    public function getEmailUser($id) {
        $id = (int) $id;

        if ($id) {
            $comments = new Zend_Db_Table('users');
            $query = "SELECT users.email  FROM users WHERE users.id = " . $id;
            $result = $comments->getAdapter()->query($query)->fetchColumn();
        }

        return $result;
    }

    public function getMessage($id) {

        $id = (int) $id;
        $table = new Model_Message();
        $select = $table->select()->where('id = ?', $id);
        $result = $table->fetchRow($select);

        return $result;
    }

    public function getMessagesUserReceived($id) {

        $id = (int) $id;
        $table = new Model_Message();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('m' => 'messages'), array('m.*'));
        $select->joinInner(array('u' => 'users'), 'm.user_from = u.id', array('u.username'));

        //check and exclude deleted messages from this user
        $select->joinLeft(array('d' => 'messages_deleted'), 'm.user_to = d.id_user and m.id = d.id_message' ,  'd.id_message' );
        $select->where('id_message IS NULL');

        $select->where('user_to = ?', $id);
        $select->order('date_created DESC');
        $result = $table->fetchAll($select)->toArray();

        return $result;
    }

    public function getMessagesUserSent($id) {

        $id = (int) $id;
        $table = new Model_Message();
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('m' => 'messages'), array('m.*'));
        $select->joinInner(array('u' => 'users'), 'm.user_to = u.id', array('u.username'));

        //check and exclude deleted messages from this user
        $select->joinLeft(array('d' => 'messages_deleted'), 'm.user_from = d.id_user and m.id = d.id_message' ,  'd.id_message' );
        $select->where('id_message IS NULL');

        $select->where('user_from = ?', $id);
        $select->order('date_created DESC');
        $result = $table->fetchAll($select)->toArray();

        return $result;
    }

    public function checkMessagesUser($id) {

        $id = (int) $id;
        $table = new Zend_Db_Table('messages');
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('m' => 'messages'), array('m.*'));

        $select->joinLeft(array('d' => 'messages_deleted'), 'm.user_to = d.id_user and m.id = d.id_message' ,  'd.id_message' );
        $select->where('id_message IS NULL');
        
        $select->where('m.user_to = ?', $id);
        $select->where('m.readed = ?', 0);

        

        return $table->fetchAll($select)->count();
        
    }

    public function updateMessageReaded($id) {
        $id = (int) $id;
        $table = new Model_Message();
        $data['readed'] = 1;
        return $table->update($data, 'id = ' . $id);
    }

    public function deleteMessage(array $data) {
        //really we never delete any message, just keep a pointer to deleted messages
        //because 1 message always have 2 user owners
        $table = new Zend_Db_Table('messages_deleted');

        return $table->insert($data);
    }

}