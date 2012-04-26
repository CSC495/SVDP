<?php
// Plugin which is registered with all Controllers.
class App_Controller_Plugin_AuthPlugin extends Zend_Controller_Plugin_Abstract
{
    private $_acl = null;
    
    // Assigns the ACL rules to be used and builds the plugin
    public function __construct(Zend_Acl $acl)
    {
        $this->_acl = $acl;
    }
    
    /*
     preDispatch will run whenever a new request is made. Function will
     validate the creditentials of the user based upon the ACL rules.
     
     IF the user has not logged on yet, they will be redirected to the login page
     
     IF the user has logged on it will verify they have the proper permissions to
     access the requested page. If they do not have permission the error page
     will be displayed.
    */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $loginController = 'login';
        $loginAction     = 'process';

        $auth = Zend_Auth::getInstance();
        // Check if user has not logged in
        if (!$auth->hasIdentity()
                && $request->getControllerName() != $loginController
                && $request->getActionName()     != $loginAction) {
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoSimpleAndExit($loginAction, $loginController);
        }

        // User is logged in or on login page.
        if ($auth->hasIdentity()) {
            // Get users identity
            $identity = $auth->getIdentity();

            // Check if role allows access to controller and action
            $isAllowed = $this->_acl->isAllowed($identity->role,
                                         $request->getControllerName(),
                                         $request->getActionName());
            
            // Check if user does not have permission and send to error page
            if (!$isAllowed) {
                $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                $redirector->gotoUrlAndExit('/error/error');
            }
        }
    }
}
