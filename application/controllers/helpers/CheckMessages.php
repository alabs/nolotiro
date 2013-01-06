<?php

class Zend_Controller_Action_Helper_CheckMessages extends Zend_Controller_Action_Helper_Abstract {

    /** Check the number of unread messages a user have.
     * We show this info permanently when the user is logged in so we use an
     * action's helper postDispatch function to update the counter after every
     * action
     */
    function postDispatch() {

        $auth = Zend_Auth::getInstance ();

        if ($auth->hasIdentity()) {

            $m_message = new Model_Message();
            $n_unread = $m_message->getUnreadCount($auth->getIdentity()->id);

            $view = $this->getActionController()->view;
            $view->n_unread = $n_unread;
        }
    }

}
