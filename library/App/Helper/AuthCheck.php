<?php

/**
 * Action Helper to simplify the common controller task of verifying that the end user has logged in
 * and has credentials required to view that page.
 */
class App_Helper_AuthCheck extends Zend_Controller_Action_Helper_Abstract
{

    /**
     * Checks that a user having the specified role is currently logged in. If no one is logged in
     * or the logged-in user has a different role, then the helper will redirect to the login
     * controller and flash a suitable error message.
     *
     * Note: Administrators are allowed to access any page, regardless of the page's required role.
     *
     * @param string $requiredRole A role constant from `Application_Model_User`.
     */
    public function direct($requiredRole)
    {
        $errorMsg     = null;
        $allowedRoles = array($requiredRole, Application_Model_User::ROLE_ADMIN);
        $authService  = new App_AuthService();

        if (!$authService->isLoggedIn()) {
            $errorMsg = 'Please log in to access that page.';
        } else if (!in_array($authService->getUser()->getRole(), allowedRoles)) {
            $errorMsg = 'You are not authorized to access that page.';
        }

        if ($errorMsg !== null) {
            $flash      = Zend_Controller_Action_HelperBroker::getStaticHelper('FlashMessenger');
            $redirector = Zend_Controller_Action_HelperBroker::getStaticHelper('Redirector');

            $flash->addMessage($errorMsg);

            $redirector->gotoSimple('index', 'login');
        }
    }
}
