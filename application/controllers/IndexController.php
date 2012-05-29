<?php
/**
 * Controller handles the request for the 'root directory' of
 * the webpage
 *
 */
class IndexController extends Zend_Controller_Action
{
	/**
	* Initalizes data needed by the IndexController
	*
	* @return null
	*/
    public function init()
    {
    }
	/**
	* Index page for the Index controller. Redirects to the login page
	*
	* @return null
	*/
    public function indexAction()
    {
        $this->_helper->redirector('login','login');
    }
}
