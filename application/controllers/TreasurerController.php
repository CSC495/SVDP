<?php

/**
 * Controller implementing functionality specific to the parish treasurer.
 */
class TreasurerController extends Zend_Controller_Action
{

    /**
     * Home page action that lists open check requests, displaying a brief summary of each request.
     */
    public function indexAction()
    {
        $this->view->pageTitle = "Open Check Requests";

        $service               = new App_Service_Search();
        $this->view->checkReqs = $service->getOpenCheckReqs();
    }
	
	public function updatefundsAction()
	{
		$this->view->pageTitle = "Update Current Funds";
		
		$request = $this->getRequest();
		$this->view->form = new Application_Model_Treasurer_FundsForm();
		
		if ($request->isPost()) {
			$this->view->form->populate($_POST);
			$service               = new App_Service_TreasurerService();
			
			$amt = $this->view->form->getValue('funds');
			$service->updateParishFunds($amt);
            return;
        }
		
	}
	
	public function checkreqAction()
	{
		$this->view->pageTitle = 'Check Request';
		
		
		
		$request = $this->getRequest();
		

		//$service           = new App_Service_Search();
        //$this->view->checkReqs = $service->getOpenCheckReqs();
		
        $service           = new App_Service_TreasurerService();
        
		$check = $service->getCheckReqById(2);
		$this->view->form = new Application_Model_Treasurer_CheckForm($check);
		
		//$this->view->form 	= new App_Model_Treasurer_CheckForm();
		
	}
}
