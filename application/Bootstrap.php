<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    Zend_Session::start();
    
    protected function _initConfig()
    {
        /*
        Load configuration data
        Zend_Registry::set('logo',$logo);
        Zend_Registry::set('organization',$logo);
        */
    }
}

