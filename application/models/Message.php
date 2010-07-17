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


    public function listMessagesUser($id) {

        $id = (int) $id;
        $table = new Model_Message();
        $select = $table->select()->where('user_to = ?', $id);
        $select->order('date_created DESC');	
        $result = $table->fetchAll ( $select )->toArray ();

        return $result;
    }



    public function checkMessagesUser($id) {

        $id = (int) $id;
        $table = new Model_Message();
        $select = $table->select()->where('user_to = ?', $id);
        return $table->fetchAll($select)->count();
    }

}

