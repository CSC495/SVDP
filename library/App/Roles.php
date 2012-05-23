<?php

/**
 * Provides constants for the possible user roles
 * Note: A user that is logged in simply has no role (null)
 */
class App_Roles
{
	/**
     * General Role. 'subclass' role of all other roles
     * @var string
     */
    const GENERAL   = "G";
	/**
     * Standard Member Role.
     * @var string
     */
    const MEMBER    = "M";
	/**
     * Treasurer Role
     * @var string
     */
    const TREASURER = "T";
	/**
     * Admin Role
     * @var string
     */
    const ADMIN     = "A";
}