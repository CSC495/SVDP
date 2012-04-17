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
        $this->view->pageTitle = "Login Page";
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
        //if( !$this->isValidPassowrd($password) )
        //{
        //    $this->_redirect('/login/index/error_flag/TRUE');
        //}
        
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
        // Get the database adapter
        $registry = Zend_Registry::getInstance();
        $adapter = new Zend_Auth_Adapter_DbTable($registry->db);
        
        // Set the parameters
        $adapter
            ->setTableName('user')
            ->setIdentityColumn('user_id')
            ->setCredentialColumn('password')
        ;
        return($adapter);
    }
    protected function authenticate($userid, $password)
    {
        $auth = Zend_Auth::getInstance();
        $authAdapter = $this->getAuthAdapter();
        
        // Set the user inputed values
        $authAdapter
            ->setIdentity($userid)
            ->setCredential($password)
        ;
        
        // Authenticate the user
        $result = $auth->authenticate($authAdapter);
        
        // Check for invalid result
        if( !$result->isValid() ){
            // User was not valid
            
            // redirect to login
            $this->_redirect('/login/index/error_flag/TRUE');
        }
        
        // Erase the password in cache.
        $data = $authAdapter->getResultRowObject(null,'password');
        $auth->getStorage()->write($data);
        
        //User was valid redirect to correct page
        $identity = Zend_Auth::getInstance()->getIdentity();
        $identity->role = $authAdapter->getResultRowObject('role')->role;

        //Redirect accordinly
        $switch( $identity->role)
        {
            case App_Roles::MEMBER:
                $this->_helper->redirector('index','member');
                break;
            case App_Roles::ADMIN:
                $this->_helper->redirector('index','admin');
                break;
            case App_Roles::TREASURER:
                $this->_helper->redirector('index','treasurer');
                break;
        }
    }

    protected function isValidPassword($password)
    {
        // Check password. Rules..
        // One digit from 0-9
        // one lowercase character
        // one uppercase character
        // and one of @,#,$,%
        // Length of 6 to 20 characters
        return(true);
        return preg_match('((?=.*\\d)(?=.*[a-z])(?=.*[A-Z])(?=.*[@#$%]).{6,20})', $password);
    }
}

