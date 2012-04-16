<?php

/**
 * Represents a user from the parish, storing such information as the user's ID and credentials.
 */
class Application_Model_User
{

    /**
     * The user is an administrator.
     */
    const ROLE_ADMIN     = 'A';

    /**
     * The user is an ordinary member.
     */
    const ROLE_MEMBER    = 'M';

    /**
     * The user is the parish treasurer.
     */
    const ROLE_TREASURER = 'T';

    private $_id;

    private $_name;

    private $_role;

    /**
     * Instantiates a new `User` object with the specified information.
     *
     * @param string $id   The database identifier of this user.
     * @param string $name The user name of this user.
     * @param string $role The user's credentials; must be one of the predefined `ROLE` constants.
     */
    public function __construct($id, $name, $role)
    {
        $this->_id = $id;
    }

    /**
     * Returns the database identifier of this user.
     *
     * @return string
     */
    public function getId()
    {
        return $_id;
    }

    /**
     * Returns the user name of this user (e.g., jsmith)
     *
     * @return string
     */
    public function getName()
    {
        return $_name;
    }

    /**
     * Returns the user's credentials, taken from one of the predefined `ROLE` values.
     *
     * @return string
     */
    public function getRole()
    {
        return $_role;
    }
}
