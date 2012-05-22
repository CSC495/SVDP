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
	$this->view->start = $start;
	$this->view->end = $end;
	$service = new App_Service_DocumentService();
	//calculate total miles for cases
	$homeVisit = 0;
	$teleVisit = 0;
	$totalMiles = 0;
	$teleHelped = 0;
	$homeHelped = 0;
	$miles = $service->getCaseVisitMiles($start, $end);
	foreach($miles as $key => $value)
	{
	    //if miles is greater than zero its a home visit
	    if($value->getTotalMiles() > 0)
	    {
		$homeVisit += 1;
		$totalMiles += $value->getTotalMiles();
		$homeHelped += $value->getNumHMembers();
	    }
	    else  //else its a phone visit
	    {
		$teleVisit += 1;
		$teleHelped += $value->getNumHMembers();
	    }
	    
	}
	$this->view->home = $homeVisit;
	$this->view->tele = $teleVisit;
	$this->view->totalMiles = $totalMiles;
	$this->view->homeHelped = $homeHelped;
	$this->view->teleHelped = $teleHelped;
	
	$totalHours = 0;
	$hours = $service->getCaseVisitHours($start, $end);
	foreach($hours as $row)
	{
	    $totalHours = $totalHours + $row;	    
	}
	$this->view->totalHours = $totalHours;
	
	$refer = $service->getGenReports($start, $end);
	$referrals = 0;
	$referHelped = 0;
	
	foreach($refer as $row)
	{
	    $referrals += $row->getNumRefs();
	    $referHelped += $row->getNumHMembers();
	}
	
	$this->view->referrals = $referrals;
	$this->view->referHelped = $referHelped;
	
	
    }
    public function reimbursementreportAction(){
	$this->view->pageTitle = "Reimbursement Report"; 
        $this->view->form = new Application_Model_Report_reimbursementReport(); 
    }
    public function reimbursementresultsAction(){
	$this->view->pageTitle = "Reimbursement Report"; 
        $this->view->form = new Application_Model_Report_reimbursementReport();
	$form->populate($_POST);
	$start = $form->startDate->getValue();
	$end = $form->endDate->getValue();
	$this->view->start = $start;
	$this->view->end = $end;
	$service = new App_Service_DocumentService();
	$miles = $service->getClosedCheckReqs($start, $end);
	
	$service = new App_Service_Member();	
	$this->view->client = $service->getClientById($cId);
     
    }

}

