<?php

class IndexController extends Zend_Controller_Action
{

    public function init()
    {
        /* Initialize action controller here */
        // Load site image into session
    }

    public function indexAction()
    {
        $this->redirect('/login/index');
    }


}

