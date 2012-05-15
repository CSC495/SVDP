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
        $form->aid->setValue($config->getLifeTimeLimit());
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
        
            // Redirect user
            $this->_forward('index', App_Resources::REDIRECT, null,
                    Array( 'msg' => 'Limits have been adjusted successfully!',
                           'time' => 3,
                           'controller' => App_Resources::ADMIN,
                           'action' => 'index'));
        }
    }

    // Displays all member information
    public function membersAction()
    {
        $this->view->pageTitle = "Admin Viewing Users";
        
        $service = new App_Service_AdminService();
        $this->view->users = $service->getAllUsers();
    }
    
    // displays view for creating new member
    public function newAction()
    {
        $request = $this->getRequest();
        
        $form = new Application_Model_Admin_NewUserForm();
        $this->view->pageTitle = "Admin New Member Contact";
        $this->view->form = $form;

        $this->view->headScript()->appendFile($this->view->baseUrl('admin.js'));
        $this->view->headScript()->appendFile($this->view->baseUrl('utility.js'));
        
        
        // Verify Post
        if( $request->isPost() ){
            $this->handleNewForm($form);
        }
        
    }

    private function handleNewForm($form)
    {
        $form->populate($_POST);
        
        $error = false;
        if(!$form->isValid($_POST))
            $error = true;
            
        // Check to ensure atleast one phone number was provided
        if($form->getValue('cell') === '' && $form->getValue('home') === '')
        {
            $form->cell->addError('');
            $form->home->addError('Either cell or home phone must be provided');
            
            $error = true;
        }
            
        // If theres an error return
        if($error)
            return;
        
        $user = new Application_Model_Impl_User();
        $user
            ->setFirstName($form->getValue('firstname'))
            ->setLastName($form->getValue('lastname'))
            ->setEmail($form->getValue('email'))
            ->setCellPhone($form->getValue('cell'))
            ->setHomePhone($form->getValue('home'))
            ->setRole($form->getValue('role'))
            ->setActive(1); // Default user to active

        $userName = substr($user->getFirstName(),0,1);
        $userName = $userName . $user->getLastName();
        $userName = strtolower($userName);
        $user->setUserId($userName);

        $password = App_Password::generatePassword(10);

        $service = new App_Service_AdminService();
        $service->createParishMemeber($user,$password);
        
        // Redirect user
        $this->_forward('index', App_Resources::REDIRECT, null,
                    Array( 'msg' => 'Member added successfully!',
                           'time' => 3,
                           'controller' => App_Resources::ADMIN,
                           'action' => 'members'));
    }
    // Display for modifying a users information
    public function modifyAction()
    {
        $this->view->pageTitle = "Admin Modify Member";
        
        // Get request and passed parameter
        $request = $this->getRequest();
        
        $form = new Application_Model_Admin_ModifyUserForm();
        // Handle display of form
        if( $request->isGet() && $this->_hasParam('id') )
        {
            $userId = $request->getParam('id');
        
        
            // Get the users data
            $service = new App_Service_AdminService();
            $user = $service->getUserInfo($userId);
        
            $this->view->form = $form;
        
            // Set form default values
            $this->view->form->userid->setValue($user->getUserId());
            $this->view->form->firstname->setValue($user->getFirstName());
            $this->view->form->lastname->setValue($user->getLastName());
            $this->view->form->email->setValue($user->getEmail());
            $this->view->form->cell->setValue($user->getCellPhone());
            $this->view->form->home->setValue($user->getHomePhone());
            $this->view->form->role->setValue($user->getRole());
            $this->view->form->status->setValue($user->getActive());
            
            return;
        }
        elseif( $request->isPost())
        {
            $this->handleModifyForm($form);
            $this->view->form = $form;
            return;
        }
        
        return $this->_helper->redirector('index'); 
    }
    
    // Validates and checks the modify member form and persists data
    private function handleModifyForm($form)
    {
        $error = false;
        // return if the form is not valid
        if( !$form->isValid($_POST) )
            $error = true;

        // Check to ensure atleast one phone number was provided
        if($form->getValue('cell') === null && $form->getValue('home') === null)
        {
            $form->cell->addError('');
            $form->home->addError('Either cell or home phone must be provided');
            
            $error = true;
        }
        
        // Return if there were any errors
        if($error)
            return $error;
        
        // Everything is good update in database
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
        
        $this->_forward('index', App_Resources::REDIRECT, null,
                        Array( 'msg' => 'Member Data Updated Successfully!',
                               'time' => 3,
                               'controller' => App_Resources::ADMIN,
                               'action' => 'members'));
        
    }
}
