<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function init(){
        Zend_Session::start();
        
        // Register the App namespace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace(array('App_'));
    }
    
    protected function _initControllerPlugins()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $acl             = new App_Acl();

        // Register the plugin for controllers which verifies authentication
        $frontController->registerPlugin(new App_Controller_Plugin_AuthPlugin($acl));

        // Register the plugin for controllers which sets navigation view parameters
        $frontController->registerPlugin(new App_Controller_Plugin_NavPlugin());
    }
  
    protected function _initParishParams()
    {
        // Ensure DB is bootstrapped first
        if( $this->getResource('db') == null);
            $this->bootstrap('db');
        
        $service = new App_Service_AdminService();
        $params = $service->getParishParams();
        Zend_Registry::set('config', $params);
    }
    
    protected function _initSchedule()
    {
        // Ensure DB is bootstrapped first
        if( $this->getResource('db') == null);
            $this->bootstrap('db');
            
        $service         = new App_Service_GeneralService();
        $scheduleEntries = $service->getScheduleEntries();
        Zend_Registry::set('schedule',$scheduleEntries);
    }
    
}

