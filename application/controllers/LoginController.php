<?php
/**
 * Class implements login functionality include logout, forgot password
 * and creation of the users inital session
 */
class LoginController extends Zend_Controller_Action
{
    // Getting user info
    // $identity = Zend_Auth::getInstance()->getIdentity();
    // $identity->user_name;
    // $identity->role;
    //
    // $identity will have a value that matches all columns in the database
    // except for the password which will not be stored
    //
    // Check if identity exists
    // Zend_Auth::getInstance()->hasIdentity();
    
    /**
     * Initializes the login controller
     *
     * 
     * @return void
     */
    public function init()
    {
        /* Initialize action controller here */
        //$this->view->pageTitle = "Login Page";
    }
    
    /**
     * Handles interface for presenting user with login form as well as login logic
     *
     * @return void
     */
    public function loginAction()
    {
        $request = $this->getRequest();
        
        // Forwards the user if they are already logged on
        $this->forwardUser();

        $this->view->form = new Application_Model_Login_LoginForm();
        $this->view->pageTitle = "Login Page";
        
        // If values have not been posted back return and render view
        if( !$request->isPost() )
            return;

        // Get form and validate it
        $form = $this->view->form;
        $form->populate($_POST);

        // Check if the password forgot button was pressed
        if($form->forgot->isChecked()){
            return $this->_helper->redirector(
                                              'forgot',
                                              App_Resources::LOGIN,
                                              null,
                                              array('prev' => 'login'));
        }

        // Validate the fields on the form
        if( !$form->isValid( $request->getPost() ) ){
            // Redirect to login page and set error
            return;
        }
        
        // Get user name and pass
        $userid = $form->getValue('username');
        $password = $form->getValue('password');

        // Try to authenticate the user
        $this->authenticate($userid, $password);
        
        
    }
    
    /**
     * Handles the interface for presenting a user with a form to reset password
     *
     * @return void
     */
    public function forgotAction()
    {
        $request = $this->getRequest();
        
        // Set page variables
        $this->view->form = new Application_Model_Login_ForgotForm();
        $this->view->pageTitle = "Forgot Password";

        // If the previous page was login, then render the view
        if( $this->_hasParam('prev') && $this->_getParam('prev') == 'login'){
            return;
        }
        
        // If this isn't a post, return to index
        if(!$request->isPost())
        {
            $this->_helper->redirector('index');
        }
        
        // Check if the form is valid.
        if( !$this->view->form->isValid( $_POST ))
        {
            // Render view with error
            return;
        }

        // find users info
        $service = new App_Service_LoginService();
        $username = $this->view->form->getValue('username');
        
        $user = $service->getUserInfo($username);
        // generate password and send e-mail if the account exists
        if($user){
            $password = App_Password::generatePassword(10);

            $mail = new Zend_Mail('utf-8');
            $transport = new App_Mail_Transport_AmazonSES(
            array(
                'accessKey' => Zend_Registry::get("AWS_ACCESS_KEY_ID"),
                'privateKey' => Zend_Registry::get("AWS_SECRET_ACCESS_KEY"),
            ));
            
            $mail->setBodyHtml('Here is your temporary password. You will be required '
                               . 'to changed it on your next login.' .
                               '<br/><b>' . $password . '</b>');
            $mail->setFrom('noreply@raphaelsvdp.org', 'System');
            $mail->addTo( $user->getEmail() );
            $mail->setSubject('SVDP Password Reset');
            $mail->send($transport);
            
            // Update DB with temp password
            $admin = new App_Service_AdminService();
            $admin->resetUserPassword($username,$password);

            return $this->_forward('index', App_Resources::REDIRECT, null,
                        Array( 'msg' => 'Your new password will be emailed to you shortly.',
                               'time' => 3,
                               'controller' => App_Resources::LOGIN,
                               'action' => 'index')); 
        }
        
        return $this->_helper->redirector('login');
    }

    /**
     * Handles the logic for logging a user out
     *
     * @return void
     */
    public function logoutAction()
    {
        // Clear credentials and redirect to login form.
        Zend_Auth::getInstance()->clearIdentity();
        $this->_helper->redirector('index');
    }
    /**
     * Handles the authentication of a user
     *
     * @usedby LoginController::processAction()
     * @param string $userid The ID that the user is trying to log in as
     * @param string $password The password the user provided to log in
     * @return void
     */
    protected function authenticate($userid, $password)
    {
        $auth = Zend_Auth::getInstance();
        
        $loginService = new App_Service_LoginService();
        $authAdapter = $loginService->getAuthAdapter($userid, $password);
        
        // Authenticate the user
        $result = $auth->authenticate($authAdapter);
        
        // Check for invalid result
        if( !$result->isValid() ){
            // This is ugly... modifies the form of the 'login' view
            return $this->view->form->err->addError('Invalid user name or password');
        }
        
        // Erase the password from the data to be stored with user
        $data = $authAdapter->getResultRowObject(null,'password');

        // Store the users data
        $auth->getStorage()->write($data);
        
        // Get the users identity
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        // Set the time out length
        $authSession = new Zend_Session_Namespace('Zend_Auth');
        $authSession->setExpirationSeconds(Zend_Registry::Get('timeout'));

        
        $this->forwardUser();
    }
    
    /**
     * Handles creation of view to change password
     *
     * @usedby Application_Model_Login_LoginForm
     * @return void
     */
    protected function changeAction()
    {
        $request = $this->getRequest();
        
        // Verify a user didn't manually navigate here when password doesn't
        // need to be changed.
        if( !Zend_Auth::getInstance()->getIdentity()->change_pswd )
        {
            $this->_helper->redirector('index');
        }
        
        $this->view->error_flag = $this->getRequest()->getParam('error_flag');
        $this->view->pageTitle = "Change Password";
        $form = new Application_Model_Login_ChangeForm();  
        $this->view->form = $form;

        // If not postback render view
        if( !$request->isPost() )
        {
            return;
        }

        //Post back, check form
        if( !$form->isValid($request->getPost()) )
        {
            return;
        }
        $pwd = $form->getValue('password');
        $vpwd = $form->getValue('verify');

        // Ensure passwords match
        if( strcmp($pwd,$vpwd) )
        {
            $form->err->addError('Passwords don\'t match.');
            return;
        }

        $identity = Zend_Auth::getInstance()->getIdentity(); 
        $service = new App_Service_LoginService();
        $service->updateUserPassword($identity->user_id,$pwd);
        $identity->change_pswd = 0;
        
        $this->_forward('index', App_Resources::REDIRECT, null,
                        Array( 'msg' => 'Your password has been changed successfully!',
                               'time' => 3,
                               'controller' => App_Resources::INDEX,
                               'action' => 'index'));
    }
    
    /**
     * Handles forwarding a user to the correct landing page
     *
     * @return void
     */
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
}

