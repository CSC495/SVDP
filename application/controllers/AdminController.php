<?php

class AdminController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageTitle = "Admin Controller";
    }
    
    // Default landing for the admin
    public function indexAction()
    {
        $this->view->pageTitle = "Admin Controller";
    }
    
    private function initAdjustmentForm($form)
    {
        $config = Zend_Registry::get('config');

        // TODO set default values
        $form->aid->setValue("$" . $config->getLifeTimeLimit());
        $form->casefund->setValue("$" .$config->getCaseFundLimit());
        $form->lifetimecases->setValue($config->getCaseLimit());
        $form->yearlycases->setValue($config->getYearlyLimit());
        
        $this->view->headScript()->appendFile($this->view->baseUrl('admin.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('utility.js'));
    }
    // Displays view for modifying limits
    public function adjustAction()
    {
        $request = $this->getRequest();
        
        $this->view->pageTitle = "Admin Limit Adjustments";
        
        $form = new Application_Model_Admin_AdjustForm();
        $this->view->form = $form;
        
        // Check the form if post
        if( $request->isPost() ){
            return $this->handleAdjustmentForm($form);
        }
        else
            $this->initAdjustmentForm($form);
            
        return;
    }
    
    private function handleAdjustmentForm($form)
    {
        if( $form->isValid($_POST))
        {
            // Get Form Values
            $lifetimeLimit = $form->getValue('aid');
            $yearlyLimit = $form->getValue('yearlycases');
            $lifetimeCases = $form->getValue('lifetimecases');
            $caseFund = $form->getValue('casefund');
            
            //TODO: Persist the data to database.
            $config = Zend_Registry::get('config');
            $config->setYearlyLimit($yearlyLimit);
            $config->setLifeTimeLimit($lifetimeLimit);
            $config->setCaseLimit($lifetimeCases);
            $config->setCaseFundLimit($caseFund);
            
            $service = new App_Service_AdminService();
            $service->updateParishParams($config);
        
            $this->_helper->redirector('index','admin');
        }
    }
    // Handles serverside creation of a new Member
    public function newmemberAction()
    {
        $request = $this->getRequest();
        
        // Verify Post
        if( !$request->isPost() ){
            return $this->_helper->redirector('index');
        }
        
        $form = new Application_Model_Admin_NewUserForm();
        $form->populate($_POST);
        
        // Get form values
        return $this->_helper->redirector('index');
    }
    
    // Displays all member information
    public function membersAction()
    {
        $this->view->pageTitle = "Admin Viewing Users";
        
        $service = new App_Service_AdminService();
        $this->view->users = $service->getParishMembers();
    }
    
    // displays view for creating new member
    public function newAction()
    {
        $this->view->pageTitle = "Admin New Member Contact";
        $this->view->form = new Application_Model_Admin_NewUserForm();

        $this->view->headScript()->appendFile($this->view->baseUrl('admin.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('utility.js'));
    }
    
    // Display for modifying a users information
    public function modifyAction()
    {
        $this->view->pageTitle = "Admin Modify Member";
        
        // Get request and passed parameter
        $request = $this->getRequest();
        $userId = $request->getParam('id');
        
        // If theres no param go back to index
        if(!$userId)
           return $this->_helper->redirector('index'); 
        
        // Get the users data
        $service = new App_Service_AdminService();
        $user = $service->getUserInfo($userId);
        
        $this->view->form = new Application_Model_Admin_ModifyUserForm();
        
        // Set form default values
        $this->view->form->userid->setValue($user->getUserId());
        $this->view->form->firstname->setValue($user->getFirstName());
        $this->view->form->lastname->setValue($user->getLastName());
        $this->view->form->email->setValue($user->getEmail());
        $this->view->form->cell->setValue($user->getCellPhone());
        $this->view->form->home->setValue($user->getHomePhone());
        $this->view->form->role->setValue($user->getRole());
        $this->view->form->status->setValue($user->getActive());
         
    }
    
    // Handles submission of user modify form
    public function modifyprocAction()
    {
        $request = $this->getRequest();
        
        // Verify Post
        if( !$request->isPost() ){
            return $this->_helper->redirector('index');
        }
        
        // Get form values
        $form = new Application_Model_Admin_ModifyUserForm();
        $form->populate($_POST);
        
        // Update in database
        $service = new App_Service_AdminService();
        
        $user = new Application_Model_Impl_User();
        $user
            ->setUserId($form->getValue('userid'))
            ->setFirstName($form->getValue('firstname'))
            ->setLastName($form->getValue('lastname'))
            ->setEmail($form->getValue('email'))
            ->setCellPhone($form->getValue('cell'))
            ->setHomePhone($form->getValue('home'))
            ->setRole($form->getValue('role'))
            ->setActive($form->getValue('status'));
        
        $service->updateUserInformation($user);
        return $this->_helper->redirector('index');
    }
}
