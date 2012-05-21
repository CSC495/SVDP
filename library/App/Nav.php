<?php

/**
 * Provides navigation link data for the various user roles.
 */
class App_Nav
{

    private static $_NAV_LINKS_BY_ROLE = array(
        // Navigation for general volunteers.
        App_Roles::MEMBER => array(
            '/member/map' => 'Map',
            '/search/member' => 'Search',
            '/member/openCases' => 'Open Cases',
            '/member/editSchedule' => 'Edit Schedule',
            '/report' => 'Reports',
            '/member/contacts' => 'Contacts',
            '/document/list' => 'Documents',
        ),
        // Navigation for administrators.
        App_Roles::ADMIN => array(
            '/admin/users' => 'Manage Users',
            '/admin/adjust' => 'Adjust Limits',
            '/document/list' => 'Documents',
        ),
        // Navigation for treasurers.
        App_Roles::TREASURER => array(
            '/treasurer' => 'Open Check Requests',
            '/treasurer/updateFunds' => 'Update Current Funds',
            '/search/treasurer' => 'Search',
            '/report' => 'Reports',
            '/document/list' => 'Documents',
        ),
    );

    /**
     * Retrieves navigation links for the given user role. Returns an array whose keys are page 
     * paths relative to the site's base URL and whose values are page names.
     *
     * @param string $role
     * @return array
     */
    public static function getNavLinksByRole($role) {
        return isset(self::$_NAV_LINKS_BY_ROLE[$role]) ? self::$_NAV_LINKS_BY_ROLE[$role] : array();
    }
}
