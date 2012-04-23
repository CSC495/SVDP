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
        $this->view->form = new Application_Model_Admin_AdminForm();
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
        $form->populate($_POST);
        
        // Get Form Values
        $lifetimeCases = $form->getValue('lifetimecases');
        $yearlyCases = $form->getValue('yearlycases');
        $aid = $form->getValue('aid');
        
        //TODO: Persist the data to database.
        
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
        $name = 
        $this->_helper->redirector('index','admin');   
    }
    
    // Displays view for modiying limits
    public function limitsAction()
    {
        $this->view->pageTitle = "Admin Limit Adjustments";
        $this->view->form = new Application_Model_Admin_AdjustForm();
        
        // TODO set default values
        $this->view->form->aid->setValue("$2000");
        $this->view->form->lifetimecases->setValue(5);
        $this->view->form->yearlycases->setValue(1);
        
    }
    
    // Displays all member information
    public function membersAction()
    {
        $this->view->pageTitle = "Admin Viewing Users";
    }
    // Display document view
    public function docsAction()
    {
        $this->view->pageTitle = "Admin Document View";
    }
    // displays view for creating new member
    public function newAction()
    {
        $this->view->pageTitle = "Admin New Member Contact";
        $this->view->form = new Application_Model_Admin_NewUserForm();
    }
    
}