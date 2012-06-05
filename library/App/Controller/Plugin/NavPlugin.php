<?php

/**
 * Controller plugin that sets view parameters for navigation view script.
 */
class App_Controller_Plugin_NavPlugin extends Zend_Controller_Plugin_Abstract
{

    /**
     * Runs after the front controller has dispatched the requested action, allowing us to determine
     * the active navigation link after any pre-dispatch forwards take place.
     *
     * @param Zend_Controller_Request_Abstract $request
     */
    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        // Get navigation links for the current user role.
        $auth     = Zend_Auth::getInstance();
        $role     = $auth->hasIdentity() ? $auth->getIdentity()->role : App_Roles::GENERAL;
        $navLinks = App_Nav::getNavLinksByRole($role);

        // Determine current path for active tab highlighting.
        $controller = $request->getControllerName();
        $action     = $request->getActionName();

        $currentPath = "/$controller";

        if ($action !== 'index') {
            $currentPath .= "/$action";
        }

        // Mark active navigation links.
        foreach ($navLinks as $key => &$val) {
            $val = array(
                'text' => $val,
                'active' => $key === $currentPath,
            );
        }

        // Propagate navigation links to the view.
        $view           = Zend_Layout::getMvcInstance()->getView();
        $view->navLinks = $navLinks;
    }
}
