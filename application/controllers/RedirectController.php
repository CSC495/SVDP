<?php

// Class provides a single action that provides a message
// and then automatically redirects a user.
class RedirectController extends Zend_Controller_Action
{
    private $_message = "You will be redirectly shortly.";
    private $_timeout = 3;
    private $_controller = App_Resources::INDEX;
    private $_action = App_Resources::INDEX;
    
    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageTitle = "";
    }
    
    // Default landing for the admin
    public function indexAction()
    {
        $request = $this->getRequest();
        
        if( !$request->isPost() )
            $this->_helper->redirector('index',App_Resources::INDEX);

        $this->view->pageTitle = "";
        
        $timeout = $this->_timeout;
        $message = $this->_message;
        $action = $this->_action;
        $controller = $this->_controller;
        
        if( $this->_hasParam('msg') && $this->_hasParam('action') && $this->_hasParam('controller') )
        {
            if( $this->_hasParam('time') )
                $timeout = $this->_getParam('time');
            
            $message = $this->_getParam('msg');
            $action = $this->_getParam('action');
            $controller = $this->_getParam('controller');
        }
        
        $this->view->message = $message;
        $this->view->controller = $controller;
        $this->view->timeout = $timeout;
        $this->view->action = $action;
        
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $this->getResponse()->setHeader('Refresh', '3; URL=' . $baseUrl->baseUrl($controller) . '/' . $action);
    }
}