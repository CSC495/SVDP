<?php

class ReportController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->pageTitle = "Report Controller";
        
        //$sessionNamespace = new Zend_Session_Namespace();
    }

    public function indexAction()
    {
        $this->view->pageTitle = "Report Controller";
         $this->view->form = new Application_Model_Report_reportForm(); 
    }
     public function processAction(){
   	$request = $this->getRequest();
    	
        // If we don't have a POST request, go back to login 
        if (!$request->isPost()) {
            return $this->_helper->redirector('index');
        }
        
        // Get our form and validate it
        $form = new Application_Model_Report_reportForm(); 
       
        //$sessionNamespace = new Zend_Session_Namespace();
        $valid = true;
 	
	$this->view->error_flag = FALSE;
	
        $form->populate($_POST);
        
	if($form->report1->isChecked())
	{
	    $this->_helper->redirector('reimburseform');
	}
	else if($form->report2->isChecked())
	{
	    $this->_helper->redirector('ocactivities');
	}
        else if($form->report3->isChecked())
	{            
	    $this->_helper->redirector('clientinfo');
	}
	    
        
    }
    public function clientinfoAction(){
	$this->view->pageTitle = "Client Information Report"; 
	//$sessionNamespace = new Zend_Session_Namespace();
        $this->view->form = new Application_Model_Report_clientReport(); 
    }
    public function ocactivitiesAction(){
	$this->view->pageTitle = "On Call Activities Report"; 
    }
    public function reimburseformAction(){
	$this->view->pageTitle = "Reimbursement Report"; 
	//$sessionNamespace = new Zend_Session_Namespace();
        $this->view->form = new Application_Model_Report_reimbursementReport(); 
    }

}

