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
        $acl = new App_Acl();
        // Register the plugin for controllers which verifies authentication
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->registerPlugin(new App_Controller_Plugin_AuthPlugin($acl));
    }
	
	public function _initActionHelpers()
    {
        Zend_Controller_Action_HelperBroker::addHelper(new App_Helper_AuthCheck());
    }

    protected function _initDbConnection()
    {
        // Specifiy database access paramters
        $options = array(
                    'host' => 'localhost', 
   		    'username' => 'webuser', 
   		    'password' => '',
   		    'dbname' => 'svdp');
        
        // Create the database Adapter
		$db = Zend_Db::factory('PDO_MYSQL', $options);
		Zend_Db_Table_Abstract::setDefaultAdapter($db);
        
        // Store the db connection in memory
        $registry = Zend_Registry::getInstance();
        $registry->set('db',$db);
    }
}

