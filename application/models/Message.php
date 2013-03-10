<?php

class Model_Message {

    /// TODO: In all models, singleton in accesing tables

    /**
     * Creates a new thread, and optionally a first message in the thread
     *
     * @param array $data: subject, users and first message content
     */
    public function createThread(array $data) {
        $threads_table = new Zend_Db_Table('threads');
        $thread_data['subject'] = $data['subject'];
        $thread_data['last_speaker'] = $data['user_from'];
        $thread_data['unread'] = '1';
        $data['thread_id'] = $threads_table->insert($thread_data);

        unset($data['subject']);
        $this->createMessage($data);

        return $data['thread_id'];
    }


    /**
     * Create a new message in an existing thread
     *
     * @param array $data
     * @return $id
     */
    public function createMessage(array $data) {
        $messages_table = new Zend_Db_Table('messages');
        $data ['date_created'] = date ( 'Y-m-d H:i:s' );
        $messages_table->insert ( $data );

        $threads_table = new Zend_Db_Table('threads');
        $thread_data = array ( 'unread' => new Zend_Db_Expr('unread + 1'),
                               'last_speaker' => $data['user_from'] );
        $where = array ( 'id = ?' => $data['thread_id'] );
        $threads_table->update($thread_data, $where);
    }


    /**
     * get threads from an user_id
     *
     * @param user_id
     */
    public function getThreadsFromUser($id) {
        if (!$id)
            return null;
        $messages_table = new Zend_Db_Table('messages');
        $select = $messages_table->select()
            ->setIntegrityCheck(false)
            ->from(array('m' => 'messages'), array (
                         'thread_id',
                         'total_messages' => '(count(*))',
                         'last_updated' => '(max(date_created))',
                         'id_with' =>
                "(CASE user_to WHEN $id THEN u2.id ELSE u1.id END)",
                         'name_with' =>
                "(CASE user_to WHEN $id THEN u2.username ELSE u1.username END)"
            ))
            ->join(array('t' => 'threads'), 'm.thread_id = t.id',
                   array('subject', 'unread', 'last_speaker'))
            ->join(array('u1' => 'users'), 'user_to = u1.id', array())
            ->join(array('u2' => 'users'), 'user_from = u2.id', array())
            ->where('user_from = ? OR user_to = ?', $id, $id)
            ->group('thread_id')
            ->order('last_updated DESC');
        $result = $messages_table->fetchAll($select)->toArray();

        return $result;
    }


    /**
     * get messages in a thread
     *
     * @param thread_id
     * @return array of messages
     */
    public function getMessagesFromThread($id) {
        if (!$id)
            return null;
        $messages_table = new Zend_Db_Table('messages');
        $select = $messages_table->select()
            ->setIntegrityCheck(false)
            ->from('messages', array (
                'user_from',
                'user_to',
                'date_created',
                'body'))
            ->where('thread_id = ?', $id)
            ->join(array('u' => 'users'), 'user_from = u.id',
                   array('username_from' => 'u.username'))
            ->order('date_created');
        $result = $messages_table->fetchAll($select)->toArray();

        return $result;
    }


    /**
     * mark a thread as read by setting the unread counter to zero
     *
     * @param thread_id
     */
    public function markAsRead($id, $user_id) {
        if (!$id)
            return null;
        $threads_table = new Zend_Db_Table('threads');
        $data = array ( 'unread' => '0' );
        $where = array ( 'id = ?' => $id,
                         'last_speaker != ?' => $user_id );
        $threads_table->update($data, $where);
    }


    /**
     * Get total number of unread messages for an user_id
     *
     * @param user_id
     */
    public function getUnreadCount($user_id) {
        if (!$user_id)
            return null;
        $messages_table = new Zend_Db_Table('messages');
        $subselect = $messages_table->select()
            ->setIntegrityCheck(false)
            ->from(array( 'm' => 'messages' ))
            ->join(array( 't' => 'threads' ), 'm.thread_id = t.id',
                array( 'unread' ))
            ->where('user_from = ? OR user_to = ?', $user_id, $user_id)
            ->where('last_speaker != ?', $user_id)
            ->group('thread_id');

        $readConf = new Zend_Config_Ini(APPLICATION_PATH . '/config/nolotiro.ini', 'production');
        $dbAdapter = Zend_Db::factory($readConf->resources->db);
        $select = $dbAdapter->select()
            ->from(array('tmp' => $subselect),
                   array( 'unread_count' => 'sum(unread)' ));

        $result = $dbAdapter->fetchOne($select);

        return (!$result) ? 0 : $result;
    }

}
