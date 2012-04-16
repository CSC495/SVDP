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
    
    // ACL
    // http://stackoverflow.com/questions/545702/help-with-zend-acl
    // http://devzone.zend.com/1258/zend_acl-and-mvc-integration-part-i-basic-use/
    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageTitle = "Login Page";
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
        $this->view->form = new Application_Model_LoginForm();
        $this->view->pageTitle = "Login Page";
    }
    
    public function forgotAction()
    {
        $this->view->form = new Application_Model_ForgotForm();
        $this->view->pageTitle = "Forgot Password";
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

        // Check if the password forgot button was pressed
        if($form->forgot->isChecked()){
            $this->_helper->redirector('forgot','login');
        }

        // Validate username and password for matching criteria
        if( !$form->isValid( $request->getPost() ) ){
            // Redirect to login page and set error flag
            $this->_redirect('/login/index/error_flag/TRUE');
        }
        
        // Get user name and pass
        $userid = $form->getValue('username');
        $password = $form->getValue('password');
        
        // Check password
        if( !$this->isValidPassowrd($password) )
        {
            $this->_redirect('/login/index/error_flag/TRUE');
        }
        
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

    protected function isValidPassword($password)
    {
        // Check password. Rules..
        // One digit from 0-9
        // one lowercase character
        // one uppercase character
        // and one of @,#,$,%
        // Length of 6 to 20 characters
        
        return preg_match('((?=.*\\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%]).{6,20})', $password);
    }
}

