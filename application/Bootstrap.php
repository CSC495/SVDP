<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function init()
    {
        Zend_Session::start();
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
        if ($this->getResource('db') === null) {
            $this->bootstrap('db');
        }

        $service = new App_Service_AdminService();
        $config  = $service->getParishParams();

        Zend_Registry::set('config', $config);
    }

    protected function _initAWSParams()
    {
        Zend_Registry::set('AWS_ACCESS_KEY_ID', getenv('AWS_ACCESS_KEY_ID'));
        Zend_Registry::set('AWS_SECRET_ACCESS_KEY', getenv('AWS_SECRET_ACCESS_KEY'));
    }

    protected function _initSchedule()
    {
        // Ensure DB is bootstrapped first
        if ($this->getResource('db') === null) {
            $this->bootstrap('db');
        }

        $service  = new App_Service_GeneralService();
        $schedule = $service->getScheduleEntries();

        Zend_Registry::set('schedule', $schedule);
    }

    protected function _initSession()
    {
        // Load options from application.ini
        $options = $this->getOptions();

        $sessionOptions = array(
            'gc_probability' => $options['resources']['session']['gc_probability'],
            'gc_divisor'     => $options['resources']['session']['gc_divisor'],
            'gc_maxlifetime' => $options['resources']['session']['gc_maxlifetime']
        );

        $idleTimeout = $options['resources']['session']['idle_timeout'];

        Zend_Session::setOptions($sessionOptions);

        Zend_Registry::set('timeout', $idleTimeout);
    }

}

