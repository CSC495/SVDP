<?php

class App_Controller_Plugin_AuthPlugin extends Zend_Controller_Plugin_Abstract
{
    private $_acl = null;
    
    public function __construct(Zend_Acl $acl)
    {
        $this->_acl = $acl;
    }
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
            if (!$isAllowed) {
                //$redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
                //$redirector->gotoUrlAndExit('/error/error/error_handler/NotAuthorizedException');
            }
        }
    }
}
