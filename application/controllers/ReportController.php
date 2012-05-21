<?php

class ReportController extends Zend_Controller_Action
{

    public function init()
    {
        $this->view->pageTitle = "Report Controller";
        
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
       
        $valid = true;
 	
	$this->view->error_flag = FALSE;
	
        $form->populate($_POST);
        
	if($form->rReport->isChecked())
	{
	    $this->_helper->redirector('reimbursementreport');
	}
	else if($form->oReport->isChecked())
	{
	    $this->_helper->redirector('ocactivities');
	}
        else if($form->cReport->isChecked())
	{            
	    $this->_helper->redirector('clientinfo');
	}
	    
        
    }
    public function clientinfoAction(){
	$this->view->pageTitle = "Client Information Report"; 
        $this->view->form = new Application_Model_Report_clientReport(); 
    }
    public function clientresultsAction(){
	$this->view->pageTitle = "Client Information Report";
	$form = new Application_Model_Report_clientReport();
	$form->populate($_POST);
	$cId = $form->clientid->getValue();
	$service = new App_Service_Member();	
	$this->view->client = $service->getClientById($cId);
	
    }
    public function ocactivitiesAction(){
	$this->view->pageTitle = "On Call Activities Report";
	$this->view->form = new Application_Model_Report_ocaReport();
    }
    public function ocactivitiesresultsAction(){
	$this->view->pageTitle = "On Call Activities Report";
	$form = new Application_Model_Report_ocaReport();
	$form->populate($_POST);
	$start = $form->startDate->getValue();
	$end = $form->endDate->getValue();
	$service = new App_Service_DocumentService();
	//calculate total miles for cases
	$homeVisit = 0;
	$teleVisit = 0;
	$miles = $service->getCaseVisitMiles($start, $end);
	foreach($miles as $row)
	{
	    //if miles is greater than zero its a home visit
	    if($row['totalMiles'] > 0)
	    {
		$homeVisit += 1;
	    }
	    else //else its a phone visit
	    {
		$teleVisit += 1;
	    }	    
	}
	$this->view->home = $homeVisit;
	$this->view->tele = $teleVisit;
	
	$totalHours = 0;
	$hours = $service->getCaseVisitHours($start, $end);
	foreach($hours as $row)
	{
	    $totalHours = $totalHours + $row;	    
	}
	$this->view->totalHours = $totalHours;
	
	
    }
    public function reimbursementreportAction(){
	$this->view->pageTitle = "Reimbursement Report"; 
        $this->view->form = new Application_Model_Report_reimbursementReport(); 
    }
     

}

