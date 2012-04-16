<?php

class LoginController extends Zend_Controller_Action
{
    // Getting user info
    // $identity = Zend_Auth::getInstance()->getIdentity();
    // $identity->username;
    // $identity->role;
    //
    // Check for person
    // Zend_Auth::getInstance()->hasIdentity();
    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageTitle = "Login page";
    }

    public function preDispath()
    {
        // Check if already logged in
        if( Zend_Auth::getInstance()->hasIdentity() )
        {
            // Redirect to their appropriate page
        }
    }
    public function indexAction()
    {
        $this->view->error_flag = $this->getRequest()->getParam('error_flag');
        
        // Check for forgot password
        if( $this->getRequest()->getParam('forgot') ){
            $this->view->form = new Application_Model_ForgotForm();
        }
        else{  
            $this->view->form = new Application_Model_LoginForm();
        }
        
        
    }
    
    public function forgotAction()
    {
        
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
        $form->populate($_POST);

        if($form->forgot->isChecked()){
            $this->_redirect('/login/index/forgot/true');
        }

        // Validate username and password for matching criteria
        if( !$form->isValid( $request->getPost() ) ){
            // Redirect to login page and set error flag
            $this->_redirect('/login/index/error_flag/TRUE');
            exit();
        }
        
        // Get user name and pass
        $userid = $form->getValue('username');
        $password = $form->getValue('password');
        
        $this->authenticate($userid, $password);

    }
    
    public function logoutAction()
    {
        // Clear credentials and redirect to login form.
        Zend_Autho::getInstance()->clearIdentity();
        $this->_helper->redirector('index');
    }
    
    protected function getAuthAdapter()
    {
        // TODO:
        // Get database adapter
        $dbAdapter = null;
        
        // Set instance parameters
        $authAdapter = new Zend_Auth_Adapter_DbTable(
            $dbAdapter,
            'users',
            'username',
            'password'
        );
        
        return($authAdapter);
    }
    protected function authenticate($userid, $password)
    {
        $auth = Zend_Auth::getInstance();
        $authAdapter = $this->getAuthAdapter();
        
        $authAdapter
            ->setIdentity($userid)
            ->setCredential($password)
        ;
        
        // Authenticate the user
        $result = $auth->authenticate($adapter);
        
        // Check for invalid result
        if( !$result->isValid() ){
            // User was not valid
            
            // redirect to login
            $this->_redirect('/login/index/error_flag/TRUE');
            //return($this->render('index') );
        }
        
        //User was valid redirect to correct page
    }


}

