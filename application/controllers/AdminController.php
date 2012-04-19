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
        $this->view->form = new Application_Model_AdminForm();
    }
    
    // Processes the users selection of what page to navigate to next
    public function processAction()
    {
        $request = $this->getRequest();
        
        // Get the form and populate it
        $form = new Application_Model_AdminForm();
        $form->populate($_POST);
        
        // Check if user wants to view user info
        if( $form->user->isChecked() ){
            $this->_helper->redirector('list','admin');
        }
        
        // Check if user wants to view funds
        if( $form->fund->isChecked() ){
            $this->_helper->redirector('fund','admin');
        }
        
        $this->_helper->redirector('index','admin');
        
    }
    
    // Displays all of the user information
    public function listAction()
    {
        
    }
    
    // Displays interface for adjusting limits()
    public function fundAction()
    {
        $this->view->pageTitle = "Adjust Funds";
        $this->view->form = new Application_Model_UpdateFundForm();
    }
    
    // Logic for updating the funds
    public function fundprocessAction()
    {
        $request = $this->getRequest();
        
        // Get the form and populate it
        $form = new Application_Model_UpdateFundForm();
        $form->populate($_POST);
        
        if( !$form->isValid( $request->getPost() ) )
        {
            // Redirect to login page and set error flag
            $this->_redirect('/admin/fund/error_flag/TRUE');
        }
        
        //TODO: Logic to update funds
        
        $this->_redirect('/admin/index/');
    }
}