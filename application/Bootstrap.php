<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function init(){
        Zend_Session::start();
        
        // Register the App namespace
        $autoloader = Zend_Loader_Autoloader::getInstance();
        $autoloader->registerNamespace(array('App_'));
        
        putenv('AWSPUB=AKIAI4KY3UJQ2ECLR5DQ');
        putenv('AWSPVT=xEL1I72MZEzyMQKfHBl5Desa+mkOtB2wgTG0omF4');
    }
    
    protected function _initControllerPlugins()
    {
        $acl = new App_Acl();
        // Register the plugin for controllers which verifies authentication
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new App_Controller_Plugin_AuthPlugin($acl));
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

