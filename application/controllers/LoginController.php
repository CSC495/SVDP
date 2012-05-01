<?php

class LoginController extends Zend_Controller_Action
{
    private $_timeout = 1440; // Time out in minutes
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
    public function loginAction()
    {
        // Forwards the user if they are already logged on
        $this->forwardUser();
        
        // Set page variables
        $this->view->error_flag = $this->getRequest()->getParam('error_flag');
        $this->view->form = new Application_Model_Login_LoginForm();
        $this->view->pageTitle = "Login Page";
    }
    
    public function forgotAction()
    {
        $this->view->form = new Application_Model_Login_ForgotForm();
        $this->view->pageTitle = "Forgot Password";
    }
    
    public function forgotprocessAction()
    {
        $request = $this->getRequest();

        // If there isnt a post request go back to index
        if( !$request->isPost() ){
            return $this->_helper->redirector('login');
        }
        
        // Get form data
        $form = new Application_Model_Login_ForgotForm();
        $form->populate($_POST);
        
        // Get user identity
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        // find users info
        $service = new App_Service_LoginService();
        $user = $service->getUserInfo($identity->user_id);
        
        // generate passwordand send e-mail
        if($user){
            $mail = new Zend_Mail();
            $mail->setBodyText('Here is your temporary password. You will be prompted to change it at next login.');
            $mail->setFrom('SVDP@noreply.com', 'System');
            $mail->setSubject('Temporary Password');
            
            $mail->send();
            
            // Update in db
        }
        
    }
    
    public function processAction()
    {
        $request = $this->getRequest();

        // If there isnt a post request go back to index
        if( !$request->isPost() ){
            return $this->_helper->redirector('login');
        }
        
        // Get form and validate it
        $form = new Application_Model_Login_LoginForm();
        $form->populate($_POST);

        // Check if the password forgot button was pressed
        if($form->forgot->isChecked()){
            $this->_helper->redirector('forgot','login');
        }

        // Validate username and password for matching criteria
        if( !$form->isValid( $request->getPost() ) ){
            // Redirect to login page and set error flag
            $this->_redirect('/login/login/error_flag/TRUE');
        }
        
        // Get user name and pass
        $userid = $form->getValue('username');
        $password = $form->getValue('password');

        // Check password
        if( !$this->isValidPasswordFormat($password) )
        {
            $this->_redirect('/login/login/error_flag/TRUE');
        }

        $this->authenticate($userid, $password);
    }
    
    public function logoutAction()
    {
        // Clear credentials and redirect to login form.
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index');
    }
    
    protected function getAuthAdapter()
    {
        $SALT = 'tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8';
        // Get the database adapter
        $db = Zend_Db_Table::getDefaultAdapter();
        $adapter = new Zend_Auth_Adapter_DbTable($db);

        // Set the parameters, user must be active.
        $adapter
            ->setTableName('user')
            ->setIdentityColumn('user_id')
            ->setCredentialColumn('password')
            ->setCredentialTreatment('? and active_flag="1"');
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
            $this->_redirect('/login/login/error_flag/TRUE');
        }
        
        // Erase the password from the data to be stored with user
        $data = $authAdapter->getResultRowObject(null,'password');

        // Store the users data
        $auth->getStorage()->write($data);
        
        // Get the users identity
        $identity = Zend_Auth::getInstance()->getIdentity();
        // Set the identities role
        $identity->role = $data->role;
        
        // Set the time out length
        $authSession = new Zend_Session_Namespace('Zend_Auth');
        $authSession->setExpirationSeconds($this->_timeout * 60);
        
        if($data->change_pswd == 1)
        {
            // Post to change password
            return $this->_forward('changepwd','login');
        }
        $this->forwardUser();
    }
    
    /**
     *  Interface for chaing a users password
     */
    protected function changepwdAction()
    {
        $request = $this->getRequest();
        
        if( !$request->isPost() ){
            return $this->_helper->redirector('login');
        }
        
        $this->view->form = new Application_Model_Login_ChangeForm();   
    }
    
    /***
     * Handles post from change of password and persists data
     */
    protected function processpwdAction()
    {
        $request = $this->getRequest();

        // If there isnt a post request go back to index
        if( !$request->isPost() ){
            return $this->_helper->redirector('login');
        }
        $service = new App_Service_LoginService();
        
        $form = new Application_Model_Login_ChangeForm();
        $form->populate($_POST);
        $pwd = $form->getValue('password');
       
        
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        $service = new App_Service_LoginService();
        $service->updateUserPassword($identity->user_id,$pwd);
        
        $this->forwardUser();
    }
    protected function forwardUser()
    {
        // If user does not have an identity return.
        if( !Zend_Auth::getInstance()->hasIdentity())
            return;
        
        $identity = Zend_Auth::getInstance()->getIdentity();
        //Redirect accordinly
        switch( $identity->role)
        {
            case App_Roles::MEMBER:
                $this->_helper->redirector('index',App_Resources::MEMBER);
                break;
            case App_Roles::ADMIN:
                $this->_helper->redirector('index',App_Resources::ADMIN);
                break;
            case App_Roles::TREASURER:
                $this->_helper->redirector('index',App_Resources::TREASURER);
                break;
            default:
                return;
        }
    }
    protected function isValidPasswordFormat($password)
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

