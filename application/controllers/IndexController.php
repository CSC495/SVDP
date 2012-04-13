<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        //$this->view->pageTitle = "Index";
    }

    public function indexAction()
    {
        $this->_helper->redirector('index','login');
    }
}
