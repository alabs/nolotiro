<?php

class Model_Ad extends Zend_Db_Table_Abstract {

    public function createAd(array $data) {
        $table = new Zend_Db_Table('ads');
        $fields = $table->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $field => $value) {
            if (!in_array($field, $fields)) {
                unset($data [$field]);
            }
        }
        return $table->insert($data);
    }

    public function updateAd(array $data, $id) {

        $table = new Zend_Db_Table('ads');
        $fields = $table->info(Zend_Db_Table_Abstract::COLS);
        foreach ($data as $field => $value) {
            if (!in_array($field, $fields)) {
                unset($data [$field]);
            }
        }
        return $table->update($data, 'id = ' . (int) $id);
    }

    public function deleteAd($id) {
        $this->delete('id =' . (int) $id);
    }

    public function getAd($id) {
        $id = (int) $id;

        $table = new Zend_Db_Table('ads');
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('a' => 'ads'), array('a.*'));
        $select->joinInner(array('u' => 'users'), 'a.user_owner = u.id', array('u.username'));
        $select->where('a.id = ?', $id);

        if (!$table->fetchRow($select)) {
            $result = null;
        } else {
            $result = $table->fetchRow($select)->toArray();
        }

        return $result;
    }

    public function getAdforSearch($id, $ad_type) {
        $id = (int) $id;
        $ad_type = (int) $ad_type;

        $table = new Zend_Db_Table('ads');
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('a' => 'ads'), array('a.*'));
        $select->joinInner(array('u' => 'users'), 'a.user_owner = u.id', array('u.username'));
        $select->joinLeft(array('c' => 'commentsAdCount'), 'a.id = c.id_comment', array('c.count as comments_count'));
        $select->joinLeft(array('r' => 'readedAdCount'), 'a.id = r.id_ad', array('r.counter as readings_count'));

        $select->where('a.id = ?', $id);
        $select->where('a.type = ?', $ad_type);

        //show only if user is active and not blocked
        $select->where('u.active = ?', 1);
        $select->where('u.locked = ?', 0);

        if (!$table->fetchRow($select)) {
            $result = null;
        } else {
            $result = $table->fetchRow($select)->toArray();
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
        $id = (int) $id;

        if ($id) {
            $comments = new Zend_Db_Table('comments');
            $query = "SELECT comments.* , users.username  FROM comments,users WHERE comments.ads_id = " . $id . " AND comments.user_owner = users.id ";
            $result = $comments->getAdapter()->query($query)->fetchAll();
        }

        return $result;
    }

    /**
     * Fetch a list of ads where woeid and ad_type matches
     *
     * @param  int $woeid
     * @param  string $ad_type
     * @param  string $status
     * @return array list of ads with this params
     */
    public function getAdList($woeid, $ad_type, $status=NULL, $limit=NULL) {
        $woeid = (int) $woeid;
        $ad_type = (string) $ad_type;

        if ($ad_type === 'give') {
            $ad_type = 1;
        }

        if ($ad_type === 'want') {
            $ad_type = 2;
        }

        $table = new Zend_Db_Table('ads');
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('a' => 'ads'), array('a.*'));
        $select->joinLeft(array('c' => 'commentsAdCount'), 'a.id = c.id_comment', array('c.count as comments_count'));
        $select->joinLeft(array('r' => 'readedAdCount'), 'a.id = r.id_ad', array('r.counter as readings_count'));
        $select->join(array('u' => 'users'), 'a.user_owner = u.id', array('u.username'));

        //show only if user is active and not blocked
        $select->where('u.active = ?', 1);
        $select->where('u.locked = ?', 0);

        $select->where('a.woeid_code = ?', $woeid);
        $select->where('a.type = ?', $ad_type);

        if ($status != NULL) {
            $select->where('a.status = ?', $status);
        } else {
            //dont list not available items by default
            $select->where('a.status != ?', 'delivered');
        }

        if ($limit != NULL) {
            $select->limit((int) $limit);
        }

        $select->order('a.date_created DESC');
        $result = $table->fetchAll($select)->toArray();

        return $result;
    }

    public function getAdListAll($ad_type, $status=NULL) {

        if ($ad_type === 'give') {
            $ad_type = 1;
        }

        if ($ad_type === 'want') {
            $ad_type = 2;
        }

        $table = new Zend_Db_Table('ads');
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('a' => 'ads'), array('a.*'));
        $select->joinLeft(array('c' => 'commentsAdCount'), 'a.id = c.id_comment', array('c.count as comments_count'));
        $select->joinLeft(array('r' => 'readedAdCount'), 'a.id = r.id_ad', array('r.counter as readings_count'));
        $select->join(array('u' => 'users'), 'a.user_owner = u.id', array('u.username'));
        //show only if user is active and not blocked
        $select->where('u.active = ?', 1);
        $select->where('u.locked = ?', 0);
        $select->where('a.type = ?', $ad_type);

        if ($status != NULL) {
            $select->where('a.status = ?', $status);
        } else {
            //dont list not available items by default
            $select->where('a.status != ?', 'delivered');
        }

        $select->order('a.date_created DESC');
        $result = $table->fetchAll($select)->toArray();

        return $result;
    }

    public function getAdListAllHome($ad_type) {

        $table = new Zend_Db_Table('ads');
        $select = $table->select()->setIntegrityCheck(false);
        $select->from(array('a' => 'ads'), array('a.*'));
        $select->join(array('u' => 'users'), 'a.user_owner = u.id', array('u.username'));
        //show only if user is active and not blocked
        $select->where('u.active = ?', 1);
        $select->where('u.locked = ?', 0);
        $select->where('a.type = ?', $ad_type);
        //dont list not available items by default
        $select->where('a.status != ?', 'delivered');

        $select->order('a.date_created DESC');
        $select->limit(10);

        $result = $table->fetchAll($select)->toArray();

        return $result;
    }


    public function getRankingWoeid( $limit=30){

        $table = new Zend_Db_Table('ads');
        $query = "SELECT woeid_code, COUNT(ads.id) AS ads_count FROM ads WHERE type = 1
        GROUP BY woeid_code ORDER BY ads_count DESC LIMIT $limit;";

        $result = $table->getAdapter()->query($query)->fetchAll();
         return $result;
    }


    public function getRankingUsers( $limit=30){

        $table = new Zend_Db_Table('ads');
        $query = "SELECT ads.user_owner, users.username AS user_name, COUNT(ads.id) AS ads_count FROM ads, users WHERE type = 1 AND
        ads.user_owner = users.id
        GROUP BY ads.user_owner ORDER BY ads_count DESC LIMIT $limit;";

        $result = $table->getAdapter()->query($query)->fetchAll();
         return $result;
    }


        /**
     * Fetch a list of ads where id_owner user matches
     *
     * @param  int $woeid
     * @return object
     */
    public function getAdUserlist($id) {
        $ads_user = new Zend_Db_Table('ads');
        $query = "SELECT ads.user_owner,ads.type,ads.woeid_code,ads.id as ad_id,ads.title,ads.body, ads.type,ads.date_created, ads.status, users.username,users.id
            FROM ads,users
                        WHERE ads.user_owner = " . $id . " AND users.id = " . $id . " ORDER BY date_created DESC";

        $result = $ads_user->getAdapter()->query($query)->fetchAll();
         return $result;
    }

    public function countReadedAd($id) {

        if ($id) {
            $table = new Zend_Db_Table('readedAdCount');
            $id = (int) $id;
            $sql = "SELECT  counter FROM readedAdCount WHERE id_ad =  $id";
            $result = $table->getAdapter()->query($sql)->fetch();
        }
        return $result;
    }

    public function updateReadedAd($id) {

        $table = new Zend_Db_Table('readedAdCount');
        $id = (int) $id;
        $sql = "INSERT INTO readedAdCount   ( id_ad, counter ) VALUES  ( $id , 1 )
                            ON DUPLICATE KEY UPDATE  counter=counter+1";
        $result = $table->getAdapter()->query($sql)->fetch();
    }

}