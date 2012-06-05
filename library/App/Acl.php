<?php

/**
 * Class implements the Access Control List (ACL) rules.
 *
 * All pages are initially blocked unless granted access via the setAccess methods
 * 
 * WARNING: ALL CONTROLS IN THE APPLICATION MUST BE ADDED AS A RESOURCE. View the
 * create resources to see how this is done.
 *
 * Examples how to set access rules
 * $this->(App_Roles::MEMBER,null,index); // Grants member access to index on all controllers
 * $this->(App_Roles::ADMIN,null,array('index','process')); // Grants admin access to all index and process actions
 * $this->(App_Roles::TREASURER,myControl); // Grants treasurer access to all actions in myControl.
 * $this->(App_Roles::TREASURER,myControl,array('index','process')); // Grants treasurer Access to index and process in myControl
 */
class App_Acl extends Zend_Acl
{
    /**
     * Default constructore for the ACL rules
     */
    public function __construct()
    {
        $this->createResources();
        $this->createRoles();

        // Set access rules
        $this->setGeneralAccess();
        $this->setMemberAccess();
        $this->setTreasurerAccess();
        $this->setAdminAccess();
        $this->setDataMigrationAccess();
    }

    /**
	 * Registers a controller as a resource.
	 *
	 * @return null
	 */
    protected function createResources()
    {
        // Specify resources and add them to 'this'
        $this->add(new Zend_Acl_Resource(App_Resources::ADMIN));
        $this->add(new Zend_Acl_Resource(App_Resources::ERROR));
        $this->add(new Zend_Acl_Resource(App_Resources::INDEX));
        $this->add(new Zend_Acl_Resource(App_Resources::LOGIN));
        $this->add(new Zend_Acl_Resource(App_Resources::MEMBER));
        $this->add(new Zend_Acl_Resource(App_Resources::SEARCH));
        $this->add(new Zend_Acl_Resource(App_Resources::TREASURER));
        $this->add(new Zend_Acl_Resource(App_Resources::REPORT));
        $this->add(new Zend_Acl_Resource(App_Resources::DOCUMENT));
        $this->add(new Zend_Acl_Resource(App_Resources::REDIRECT));
        $this->add(new Zend_Acl_Resource(App_Resources::MIGRATION));
    }

    /**
	 * Creates the different roles which will be used to determine access restrictions.
	 * Users that are not logged in have no role (null)
	 *
	 * @return null
	 */
    protected function createRoles()
    {
        // Create the general role which will be inherited by all
        $this->addRole(new Zend_Acl_Role(App_Roles::GENERAL));
        // Create memeber role
        $this->addRole(new Zend_Acl_Role(App_Roles::MEMBER),App_Roles::GENERAL);
        // create Treasurer Role
        $this->addRole(new Zend_Acl_Role(App_Roles::TREASURER),App_Roles::GENERAL);
        // Admin roles
        $this->addRole(new Zend_Acl_Role(App_Roles::ADMIN),App_Roles::GENERAL);
        // Create Migration Role
        $this->addRole(new Zend_Acl_Role(App_Roles::DATAMIGRATION),App_Roles::GENERAL);
    }

	/**
	 * Sets the access for the GENERAL role. This role defines access that is
	 * granted to all roles except users who are not logged in.
	 *
	 * @return null
	 */
    protected function setGeneralAccess()
    {
        // Allow access to all actions in the index, login, error, and redirect controllers
        $this->allow(App_Roles::GENERAL,App_Resources::LOGIN);
        $this->allow(App_Roles::GENERAL,App_Resources::INDEX);
        $this->allow(App_Roles::GENERAL,App_Resources::ERROR);
        $this->allow(App_Roles::GENERAL,App_Resources::REDIRECT);
        // All logged in users have access to listing and displaying documents
        $this->allow(App_Roles::GENERAL,App_Resources::DOCUMENT,array('display','list'));
    }
	/**
	 * Sets the access for the MEMBER role.
	 *
	 * @return null
	 */
    protected function setMemberAccess()
    {
        // Allow access to all actions in member controller
        $this->allow(App_Roles::MEMBER,App_Resources::MEMBER);
        // Allow access to member search pages
        $this->allow(App_Roles::MEMBER,App_Resources::SEARCH,array(
            App_Resources::INDEX,
            App_Resources::MEMBER,
        ));
        // Allow access to check request view action in the treasurer controller
        $this->allow(App_Roles::MEMBER,App_Resources::TREASURER,'view');
        // Allow access to all actions in the reports controller
        $this->allow(App_Roles::MEMBER,App_Resources::REPORT);
    }
	/**
	 * Sets the access for the TREASURER role.
	 *
	 * @return null
	 */
    protected function setTreasurerAccess()
    {
        // Allow access to all actions in the treasurer controller
        $this->allow(App_Roles::TREASURER,App_Resources::TREASURER);
        // Allow access to treasurer search pages
        $this->allow(App_Roles::TREASURER,App_Resources::SEARCH,array(
            App_Resources::INDEX,
            App_Resources::TREASURER,
        ));
        // Allow access to client and case view actions in the member controller
        $this->allow(App_Roles::TREASURER,App_Resources::MEMBER,array(
            'viewClient',
            'editClient',
            'clientHistory',
            'viewCase',
        ));
        // Allow access to all actions in the reports controller
        $this->allow(App_Roles::TREASURER,App_Resources::REPORT);
    }
	/**
	 * Sets the access for the ADMIN role.
	 *
	 * @return null
	 */
    protected function setAdminAccess()
    {
        // Allow access to all actions in admin controller
        $this->allow(App_Roles::ADMIN,App_Resources::ADMIN);
        // Allow access to all actions in document controller
        $this->allow(App_Roles::ADMIN,App_Resources::DOCUMENT);
    }
    	/**
	 * Sets the access for the DATA MIGRATION role
	 *
	 * @return null
	 */
    protected function setDataMigrationAccess()
    {
        // Allow access to migration controller
        $this->allow(App_Roles::DATAMIGRATION,App_Resources::MIGRATION);
    }
}
