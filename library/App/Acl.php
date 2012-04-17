<?php

class App_Acl extends Zend_Acl
{
    public function __construct()
    {
        $this->createResources();
        $this->createRoles();
        
        $this->setGuestAccess();
        $this->setMemberAccess();
        $this->setTreasurerAccess();
        $this->setAdminAccess();
    }
    protected function createResources()
    {
        // Specify resources
        $this->add(new Zend_Acl_Resource(App_Resources::ADMIN));
        $this->add(new Zend_Acl_Resource(App_Resources::ERROR));
        $this->add(new Zend_Acl_Resource(App_Resources::INDEX));
        $this->add(new Zend_Acl_Resource(App_Resources::LOGIN));
    }
    protected function createRoles()
    {
        // Create Guest Role
        $this->addRole(new Zend_Acl_Role(App_Roles::GUEST));
        // Create memeber role with privilages of Guest
        $this->addRole(new Zend_Acl_Role(App_Roles::MEMBER),App_Roles::GUEST);
        // create Treasurer Role, inherits guest
        $this->addRole(new Zend_Acl_Role(App_Roles::TREASURER),App_Roles::GUEST);
        // Admin roles, inherits guest
        $this->addRole(new Zend_Acl_Role(App_Roles::ADMIN),App_Roles::GUEST);
    }
    // Add access to controls here.
    // $this->(App_Roles::MEMBER,null,index); // Grants member access to index on all controllers
    // $this->(App_Roles::ADMIN,null,array('index','process')); // Grants admin access to all index and process actions
    // $this->(App_Roles::TREASURER,myControl); // Grants treasurer access to all actions in myControl.
    // $this->(App_Roles::TREASURER,myControl,array('index','process')); Access to index and process in myControl
    protected function setGuestAccess()
    {
        $this->allow(App_Roles::GUEST,App_Resources::LOGIN);
    }
    protected function setMemberAccess()
    {
        
    }
    protected function setTreasurerAccess()
    {
        
    }
    protected function setAdminAccess()
    {
        $this->allow(App_Roles::ADMIN,App_Resources::ADMIN);
    }
}