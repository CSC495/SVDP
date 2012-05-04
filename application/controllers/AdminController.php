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
    
    // Processes the users selection of what page to navigate to next
    public function processAction()
    {
        $request = $this->getRequest();
        
        if( !$request->isPost() ){
            return $this->_helper->redirector('index');
        }

        // Get the form and populate it
        $form = new Application_Model_Admin_AdminForm();
        $form->populate($_POST);
        
        // Check if user wants to view user info
        if( $form->user->isChecked() ){
            $this->_helper->redirector('members','admin');
        }
        
        // Check if user wants to adjust limits
        if( $form->adjust->isChecked() ){
            $this->_helper->redirector('limits','admin');
        }
        
        $this->_helper->redirector('index','admin');
        
    }
    // Displays view for modifying limits
    public function adjustAction()
    {
        $request = $this->getRequest();
        
        // Verify Post
        if( !$request->isPost() ){
            return $this->_helper->redirector('index');
        }
        
        // Get the form and populate it
        $form = new Application_Model_Admin_AdjustForm();
        
        if( !$form->isValid($request->getPost()) ){
            //Redirect and indicate errors   
        }
        
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
        //
        
        $this->_helper->redirector('index','admin');   
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
    
    // Displays view for modiying limits
    public function limitsAction()
    {
        $this->view->pageTitle = "Admin Limit Adjustments";
        $this->view->form = new Application_Model_Admin_AdjustForm();
        
        $config = Zend_Registry::get('config');

        // TODO set default values
        $this->view->form->aid->setValue("$" . $config->getLifeTimeLimit());
        $this->view->form->casefund->setValue("$" .$config->getCaseFundLimit());
        $this->view->form->lifetimecases->setValue($config->getCaseLimit());
        $this->view->form->yearlycases->setValue($config->getYearlyLimit());
        

        $this->view->headScript()->appendFile($this->view->baseUrl('admin.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('utility.js'));
        
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
        if($userId == "")
            $this->_helper->redirector('index','admin');  
        
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
