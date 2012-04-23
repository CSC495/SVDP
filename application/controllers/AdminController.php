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
        
        if( !$request->isPost() ){
            return $this->_helper->redirector('index');
        }

        // Get the form and populate it
        $form = new Application_Model_AdminForm();
        $form->populate($_POST);
        
        // Check if user wants to view user info
        if( $form->user->isChecked() ){
            $this->_helper->redirector('listusers','admin');
        }
        
        // Check if user wants to adjust limits
        if( $form->adjust->isChecked() ){
            $this->_helper->redirector('adjust','admin');
        }
        
        $this->_helper->redirector('index','admin');
        
    }
    // Displays view for modifying limits
    public function adjustAction()
    {
        $this->view->pageTitle = "Admin Limit Adjustments";
        $this->view->form = new Application_Model_AdjustForm();       
    }
    // Handles persistance of adjustment
    public function adjustprocessAction()
    {
        $request = $this->getRequest();
        
        // Verify Post
        if( !$request->isPost() ){
            return $this->_helper->redirector('index');
        }
        
        // Get the form and populate it
        $form = new Application_Model_AdjustForm();
        $form->populate($_POST);
        
        $this->_helper->redirector('index','admin');
        
    }
    // Displays all member information
    public function listusersAction()
    {
        
    }
    
}