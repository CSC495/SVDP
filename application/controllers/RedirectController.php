<?php

/**
 * Class provides a single action that provides a message
 * and then automatically redirects a user.
 */
class RedirectController extends Zend_Controller_Action
{
    /**
     * Message to be displayed to the user
     * @var string
     */
    private $_message = "You will be redirectly shortly.";
	/**
     * Time to wait before redirecting
     * @var int
     */
    private $_timeout = 3;
	/**
     * Controller to redirect to
     * @var string
     */
    private $_controller = App_Resources::INDEX;
	/**
     * Action to redirect to
     * @var string
     */
    private $_action = App_Resources::INDEX;
    
	/**
	 * Initalizes global data needed by each use of controller
	 *
	 * @return null
	 */
    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageTitle = "";
    }
    
    /**
	 * Displays a message and then redirects the user automatically. Provides
	 * a link to redirect if the users browser does not support redirect
	 *
	 * @return null
	 */
    public function indexAction()
    {
        $request = $this->getRequest();
        
        if( !$request->isPost() )
            $this->_helper->redirector('index',App_Resources::INDEX);

        $this->view->pageTitle = "";
        
		// Set all parameters to default
        $timeout = $this->_timeout;
        $message = $this->_message;
        $action = $this->_action;
        $controller = $this->_controller;
        
		// Get parameter values that have been passed to controller via POST
        if( $this->_hasParam('msg') && $this->_hasParam('action') && $this->_hasParam('controller') )
        {
            if( $this->_hasParam('time') )
                $timeout = $this->_getParam('time');
            
            $message = $this->_getParam('msg');
            $action = $this->_getParam('action');
            $controller = $this->_getParam('controller');
        }
        
		// Set view vars
        $this->view->message = $message;
        $this->view->controller = $controller;
        $this->view->action = $action;
        
		// Create the auto redirect tag in the HTML doc
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $this->getResponse()->setHeader('Refresh', $timeout . '; URL=' . $baseUrl->baseUrl($controller) . '/' . $action);
    }
}
