<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function init()
    {
        Zend_Session::start();
    }

    public function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new App_Helper_AuthCheck());
    }
}

