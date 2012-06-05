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
		$this->view->pageTitle = "Update Total Funds";
		
		$request = $this->getRequest();
		$this->view->form = new Application_Model_Treasurer_FundsForm();
		
		if ($request->isPost()) {
			$this->view->form->populate($_POST);
			$service = new App_Service_TreasurerService();
			
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
				if($request->checkNum == null){
					$this->_helper->flashMessenger(array(
						'type' => 'error',
						'text' => 'Real Check Number must be Set to Issue the Check Request',
					));
				}
				else{
				
					$service->closeCheckRequest(Zend_Auth::getInstance()->getIdentity()->user_id, 
													$request->checkID, $request->checkNum);
					$this->_helper->redirector('index');
				}
			}
			
			if($request->denyCheck){
				$service->denyCheckRequest($request->checkID);
				$this->_helper->redirector('index');
			}
			
			if($request->editCheck){
				if($request->payeeName == null || $request->payeeAccount == null){
					if($request->payeeName == null){
						$this->_helper->flashMessenger(array(
							'type' => 'error',
							'text' => 'Payee Name cannot be left blank',
						));
					}
					if($request->payeeAccount == null){
						$this->_helper->flashMessenger(array(
							'type' => 'error',
							'text' => 'Payee Account # cannot be left blank',
						));
					}
					$this->view->form->editCheckReq('Edit Check Request', $check);
				}
				else {
					$ret = $this->view->form->editCheckReq($request->editCheck, $check);
					$ret->setUserId($ret->getUser());
					
					$service->updateCheckRequest($ret);
					
					if($request->editCheck === 'Submit Edits'){
						$this->_helper->redirector('index');
					}
				}
			}
			
			if($request->addComment){
				// Re-add existing form data.
				$data = $request->getPost();

				$com = $this->view->form->addAComment($request->addComment);
				
				$service->updateCheckReqComment($com, $request->checkID);
			}
        }
	}
}
