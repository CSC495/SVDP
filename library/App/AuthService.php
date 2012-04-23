<?php

/**
 * Service that wraps database and session functionality pertaining to end user authentication
 * (logging in and out, checking roles, etc.).
 */
class App_AuthService
{

    private $_session;

    private $_db;

    private $_user;

    /**
     * Instantiates a new `App_AuthService` object.
     */
    public function __construct()
    {
        $this->_session = new Zend_Session_Namespace('SVDP_AuthService');
        $this->_db      = Zend_Db_Table::getDefaultAdapter();

        // If a user is currently logged in, we'll want to keep track of who they are and what
        // they're allowed to do.
        if (isset($this->_session->userId)) {
            $this->_makeUser();
        }
    }

    /**
     * Attempts to log in as the specified user.
     *
     * XXX: Database access is currently stubbed out.
     *
     * @param string $userName The user name of the user who wants to log in.
     * @param string $password The user's supposed password.
     * @return bool True if the user was successfully logged in and false if either the user name or
     * password was invalid.
     */
    public function login($userName, $password)
    {
        // TODO: Actually check password against the database's `user` table and pull user info from
        // there. :)

        $this->_session->userId   = '1';
        $this->_session->userName = 'test';
        $this->_session->userRole = self::ROLE_ADMIN;

        $this->_makeUser();

        return true;
    }

    /**
     * Logs the end user out, deleting any user-specific data from the session. If nobody is
     * currently logged in, then nothing happens.
     */
    public function logout()
    {
        unset($this->_session->userId, $this->_session->userName, $this->_session->userRole);
        $this->_user = null;
    }

    /**
     * Returns true if there is currently a user logged in and false otherwise.
     *
     * @return bool
     */
    public function isLoggedIn()
    {
        return $this->_user !== null;
    }

    /**
     * Returns information on the current user, or `null` if no one is logged in.
     *
     * @return Application_Model_user|null
     */
    public function getUser()
    {
        return $this->_user;
    }

    private function _makeUser()
    {
        $_this->_user = new Application_Model_User(
            $this->_session->userId,
            $this->_session->userName,
            $this->_session->userRole
        );
    }
}
