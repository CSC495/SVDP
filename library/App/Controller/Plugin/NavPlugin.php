<?php

/**
 * Controller plugin that sets view parameters for navigation view script.
 */
class App_Controller_Plugin_NavPlugin extends Zend_Controller_Plugin_Abstract
{

    public function postDispatch(Zend_Controller_Request_Abstract $request)
    {
        $view = Zend_Layout::getMvcInstance()->getView();

        $view->nav = array(
            'controller' => $request->getControllerName(),
            'action' => $request->getActionName(),
        );
    }
}
