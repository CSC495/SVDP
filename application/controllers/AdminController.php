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
        $this->view->pageTitle = "Admin Controller";
        $auth = Zend_Auth::getInstance();
        $this->view->role = $auth->getIdentity()->role;
    }
}