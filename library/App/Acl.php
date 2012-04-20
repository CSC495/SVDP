<?php
/*
 Class implements the ACL rules.
 
 All pages are initially blocked unless granted
 access via the setAccess methods.
*/
class App_Acl extends Zend_Acl
{
    public function __construct()
    {
        $this->createResources();
        $this->createRoles();
        
        // Set access rules
        $this->setGeneralAccess();
        $this->setMemberAccess();
        $this->setTreasurerAccess();
        $this->setAdminAccess();
    }
    // Registers the resources with the ACL.
    // Resoruces are simply the string name of a specific controller
    protected function createResources()
    {
        // Specify resources
        $this->add(new Zend_Acl_Resource(App_Resources::ADMIN));
        $this->add(new Zend_Acl_Resource(App_Resources::ERROR));
        $this->add(new Zend_Acl_Resource(App_Resources::INDEX));
        $this->add(new Zend_Acl_Resource(App_Resources::LOGIN));
        $this->add(new Zend_Acl_Resource(App_Resources::MEMBER));
        $this->add(new Zend_Acl_Resource(App_Resources::SEARCH));
    }
    // Create the various roles
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
    }
    // Add access to controls here.
    // $this->(App_Roles::MEMBER,null,index); // Grants member access to index on all controllers
    // $this->(App_Roles::ADMIN,null,array('index','process')); // Grants admin access to all index and process actions
    // $this->(App_Roles::TREASURER,myControl); // Grants treasurer access to all actions in myControl.
    // $this->(App_Roles::TREASURER,myControl,array('index','process')); Access to index and process in myControl
    protected function setGeneralAccess()
    {
        // Allow access to all actions in the index and login controller
        $this->allow(App_Roles::GENERAL,App_Resources::LOGIN);
        $this->allow(App_Roles::GENERAL,App_Resources::INDEX);
    }
    protected function setMemberAccess()
    {
        // Allow access to all actions in memebr controller
        $this->allow(App_Roles::MEMBER,App_Resources::MEMBER);
        // Allow access to member search pages
        $this->allow(App_Roles::MEMBER,App_Resources::SEARCH,array(
            App_Resources::INDEX,
            App_Resources::MEMBER,
        ));
    }
    protected function setTreasurerAccess()
    {
        // Allow access to treasurer search pages
        $this->allow(App_Roles::TREASURER,App_Resources::SEARCH,array(App_Resources::TREASURER));
    }
    protected function setAdminAccess()
    {
        // Allow access to all actions in admin controller
        $this->allow(App_Roles::ADMIN,App_Resources::ADMIN);
    }
}
