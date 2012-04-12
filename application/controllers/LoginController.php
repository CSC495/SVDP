<?php

class LoginController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        $this->view->pagetitle = "Login page";
    }

    public function indexAction()
    {
        $this->view->error_flag = $this->getRequest()->getParam('error_flag');
        $this->view->form = new Application_Model_LoginForm();
    }
    
    public function processAction()
    {
        $request = $this->getRequest();
    
        // If there isnt a post request go back to index
        if( !$request->isPost() ){
            return $this->_helper->redirector('index');
        }
        
        // Get form and validate it
        $form = new Application_Model_LoginForm();
        
        // Validate username and password for matching criteria
        if( !$form->isValid( $request->getPost() ) ){
            // Redirect to login page and set error flag
            $this->_redirect('/login/index/error_flag/TRUE');
            exit();
        }
        
        // Get user name and pass
        $userid = $form->getValue('username');
        $password = $form->getValue('password');
        
        // Setting Sessions Username
        $sessionNamespace = new Zend_Session_Namespace();
        $sessionNamespace-> userID = $userid;
        $valid = true;
        
        // Check if user name and what not is valid.
    }


}

