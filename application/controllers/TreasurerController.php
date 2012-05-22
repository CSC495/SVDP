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

        $service = new App_Service_Search();
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
			$this->_helper->redirector('index');
            return;
        }
		
	}
	
	public function checkreqAction()
	{
		$this->view->pageTitle = 'Check Request';
		
		$request = $this->getRequest();
		$service = new App_Service_TreasurerService();
			
		
		if(!$request->isPost()) {
			$check = $service->getCheckReqById($request->id);
			$this->view->form = new Application_Model_Treasurer_CheckForm($check);
		}
		
		
		if ($request->isPost()) {
		
			$check = $service->getCheckReqById($request->checkID);
			$this->view->form = new Application_Model_Treasurer_CheckForm($check);

			$this->view->form->populate($_POST);
			
			
			if($request->issueCheck){
				$service->closeCheckRequest(27, $request->checkID, $request->checkNum);
				$this->_helper->redirector('index');
			}
			if($request->denyCheck){
				//do something here, what I am not sure yet
			}
			if($request->editCheck){
				$this->view->form->editCheckReq($request->editCheck);
			}
			if($request->addComment){
				$this->view->form->addAComment($request->addComment);
			}
			
            return;
        }
		
	}
}
