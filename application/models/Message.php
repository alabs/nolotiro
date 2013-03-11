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
        $thread_data['user_from'] = $data['user_from'];
        $thread_data['user_to'] = $data['user_to'];
        $data['thread_id'] = $threads_table->insert($thread_data);

        unset($data['subject']);
        $this->createMessage($data);

        return $data['thread_id'];
    }

    /**
     * Deletes a new thread, by marking it as deleted. If both users deleted it
     * it is phisically deleted as well
     *
     * @param array $data: thread_id and user_id who is deleting
     */
    public function deleteThread(array $data) {

        /* Fetch thread */
        $threads_table = new Zend_Db_Table('threads');
        $select = $threads_table->select()->where('id = ?', $data['thread_id']);
        $thread = $threads_table->fetchRow($select);

        /* Update flags */
        if ($thread->user_from == $data['user_id'])
            $thread->deleted_from = 1;
        elseif ($thread->user_to == $data['user_id'])
            $thread->deleted_to = 1;
        else
            return null;
        $thread->save();

        /* If both deleted, delete physically */
        if ($thread->deleted_from && $thread->deleted_to) {
            $messages_table = new Zend_Db_Table('messages');
            $whereM = $messages_table->getAdapter()->quoteInto('id = ?', $data['thread_id']);
            $messages_table->delete($whereM);
            $thread->delete();
        }
        return;
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
                               'deleted_to' => 0,
                               'last_speaker' => $data['user_from'] );
        $where = array ( 'id = ?' => $data['thread_id'] );
        $threads_table->update($thread_data, $where);
    }


    /**
     * get thread of specific id
     *
     * @param id
     */
    public function getThreadFromId($id) {
        if (!$id)
            return null;
        $threads_table = new Zend_Db_Table('threads');
        $select = $threads_table->select()->where('id = ?', $id);
        return $threads_table->fetchRow($select);
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
            ->from(array('t' => 'threads'),
                   array('subject',
                         'unread',
                         'last_speaker',
                         'id_with' =>
                "(CASE t.user_to WHEN $id THEN u2.id ELSE u1.id END)",
                         'name_with' =>
                "(CASE t.user_to WHEN $id THEN u2.username ELSE u1.username END)"))
            ->join(array('m' => 'messages'), 't.id = m.thread_id',
                   array('thread_id',
                         'total_messages' => '(count(*))',
                         'last_updated' => '(max(date_created))'))
            ->join(array('u1' => 'users'), 't.user_to = u1.id', array())
            ->join(array('u2' => 'users'), 't.user_from = u2.id', array())
            ->where('t.user_from = ? AND t.deleted_from = 0', $id)
            ->orWhere('t.user_to = ? AND t.deleted_to = 0', $id)
            ->group('t.id')
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
            ->where('last_speaker != ? AND t.user_from = ? AND t.deleted_from = 0', $user_id, $user_id)
            ->orWhere('last_speaker != ? AND t.user_to = ? AND t.deleted_to = 0', $user_id, $user_id)
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
