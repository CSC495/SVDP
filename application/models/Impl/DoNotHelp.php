<?php

/**
 * Model class that represents an entry in the parish's do-not-help list.
 */
class Application_Model_Impl_DoNotHelp
{

    /**
     * The member user who first added the client to the do-not-help list.
     *
     * @var Application_Model_Impl_User
     */
    private $_user;

    /**
     * The date when the client was first added to the do-not-help list.
     *
     * @var string
     */
    private $_dateAdded;

    /**
     * The reason the client should not be helped.
     *
     * @var string
     */
    private $_reason;

    /**
     * Returns the user who added this do-not-help list entry.
     *
     * @return Application_Model_Impl_User
     */
    public function getUser()
    {
        return $this->_user;
    }

    /**
     * Sets the user who added this do-not-help list entry.
     *
     * @param Application_Model_Impl_User $user
     * @return self
     */
    public function setUser(Application_Model_Impl_User $user)
    {
        $this->_user = $user;
        return $this;
    }

    /**
     * Returns the date when the client was first added to the do-not-help list.
     *
     * @return string
     */
    public function getDateAdded()
    {
        return $this->_dateAdded;
    }

    /**
     * Sets the date when the client was first added to the do-not-help list.
     *
     * @param string $dateAdded
     * @return self
     */
    public function setDateAdded($dateAdded)
    {
        $this->_dateAdded = $dateAdded;
        return $this;
    }

    /**
     * Returns the reason why the client should not be helped.
     *
     * @return string
     */
    public function getReason()
    {
        return $this->_reason;
    }

    /**
     * Sets the reason why the client should not be helped.
     *
     * @param string $reason
     * @return self
     */
    public function setReason($reason)
    {
        $this->_reason = $reason;
        return $this;
    }
}
