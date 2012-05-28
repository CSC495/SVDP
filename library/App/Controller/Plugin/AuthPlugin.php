<?php
/**
 * Plugin is registered with all plguins. Implements a predispatch method
 * which will be run whenever a page is requested. This will ensure nobody
 * gets access to a page they shouldn't be on
 */
class App_Controller_Plugin_AuthPlugin extends Zend_Controller_Plugin_Abstract
{
	/**
	 * The ACL rules to use
	 * @var App_Acl
	 */
    private $_acl = null;
    
    /**
	 * Constructor for the plugin. Registers the ACL rules
	 * 
	 * @param Zend_Acl $acl The rules to be used with this plugin
	 */
    public function __construct(Zend_Acl $acl)
    {
        $this->_acl = $acl;
    }
    
    /**
     * preDispatch will run whenever a new request is made. Function will
     * validate the creditentials of the user based upon the ACL rules.
     *
     * IF the user has not logged on yet, they will be redirected to the login page
     *
     * IF the user has logged on it will verify they have the proper permissions to
     * access the requested page. If they do not have permission the error page
     * will be displayed.
	 *
	 * @param Zend_Controller_Request_Abstract $request The request which is being processed
     */
    public function preDispatch(Zend_Controller_Request_Abstract $request)
    {
        $auth = Zend_Auth::getInstance();
        
        // Check if user has not logged in
        if (!$auth->hasIdentity()
                && $request->getControllerName() !== App_Resources::LOGIN
                && $request->getActionName()     !== 'login') {

            //Ensure non logged in user was not going to redirect page. Everyone has access to redirect page
			// including non logged in users so just return if they want to go there.
            if($request->getControllerName() === App_Resources::REDIRECT)
                return;
            
			// The user is not logged in and was not going to redirect or login page. Send them back
			// to the login page.
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');
            $redirector->gotoSimpleAndExit('login', App_Resources::LOGIN);
        }

        // User is logged in
        if ($auth->hasIdentity()) {
            // Reset the timeout
            $authSession = new Zend_Session_Namespace('Zend_Auth');
            $authSession->setExpirationSeconds(Zend_Registry::Get('timeout'));
            
            // Get users identity
            $identity = $auth->getIdentity();
            // Send user to change password page if change is required. User is only allowed to
			// logout or change their password if a change is required
            if( $identity->change_pswd
                    && ($request->getControllerName() !== App_Resources::LOGIN
                    || $request->getActionName() !== 'change')
                    && $request->getActionName() !== 'logout'){
                
                $request->setControllerName(App_Resources::LOGIN)
                        ->setActionName('change');
            }
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
