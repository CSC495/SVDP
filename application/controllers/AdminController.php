<?php

class AdminController extends Zend_Controller_Action
{
    public function init()
    {
        /* Initialize action controller here */
        $this->view->pageTitle = "Admin Controller";
    }
    
    public function indexAction()
    {
        $this->_helper->redirector('index','login');
    }
}