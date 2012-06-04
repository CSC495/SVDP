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
		
		if ($request->isPost() && $this->view->form->isValid($_POST) ) {
			$service = new App_Service_TreasurerService();
			
			$amt = $this->view->form->getValue('funds');
			$service->updateParishFunds($amt);
                        
                        return $this->_forward('index', App_Resources::REDIRECT, null,
                        Array( 'msg' => 'Funds Updated Successfully!',
                               'time' => 2,
                               'controller' => App_Resources::TREASURER,
                               'action' => 'index')); 
                }
		
	}
	
	public function checkreqAction()
	{
            $this->view->pageTitle = 'Check Request';
            
            $request = $this->getRequest();
            $service = new App_Service_TreasurerService();
                    
            
            if(!$request->isPost()) {
                    $check = $service->getCheckReqById($request->id);
                    $this->view->form = new Application_Model_Treasurer_CheckForm($check,Application_Model_Treasurer_CheckForm::INITIAL);
            }
            
            
            if ($request->isPost()) {
            
                    $check = $service->getCheckReqById($request->checkID);
                    $this->view->form = new Application_Model_Treasurer_CheckForm($check);

                    $this->view->form->populate($_POST);
                    
                    var_dump($this->view->form);
                    exit();
                    if($request->issueCheck){
                            $service->closeCheckRequest(27, $request->checkID, $request->checkNum);
                            $this->_helper->redirector('index');
                    }
                    if($request->denyCheck){
                            $service->denyCheckRequest($request->checkID);
                            $this->_helper->redirector('index');
                    }
                    if($request->editCheck === 'Edit Check Request'){
                        $this->view->form->editCheckReq($request->editCheck);
                    }
                    if($request->editCheck === 'Submit Edits'){
                            var_dump('this should persist');
                            exit();
                    }
                    if($request->addComment){
                            $this->view->form->addAComment($request->addComment);
                    }
			
            return;
        }
		
	}
}
