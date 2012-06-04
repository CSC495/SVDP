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
    /**
     * Method handles the members view of the check request
     */
    public function viewAction()
    {
        $this->view->pageTitle = 'Viewing Check Request';
        $request = $this->getRequest();
        
        // Check if there is param
        if( !$request->id )
            $this->_helper->redirector('index');
            
        // Get check request
        $service = new App_Service_TreasurerService();
        $check = $service->getCheckReqById($request->id);
        $this->view->form = new Application_Model_Treasurer_CheckForm($check);
        
        // Set forms action to this action
        $baseUrl = new Zend_View_Helper_BaseUrl();
        $action = $baseUrl->baseUrl(App_Resources::TREASURER) . '/view';
        $this->view->form->setAction($action);
            
        if(!$request->isPost()) {
            $this->view->form->setMemberView();
        }
        
        if($request->isPost() && $this->view->form->isValid($_POST)){            

            $this->view->form->populate($_POST);
            $action = $this->view->form->getAction();
            
            switch( $action )
            {
                case 'add_comment':
                    $this->view->form->setCommentState();
                    return;
                case 'submit_comment':
                    $this->submitComment($this->view->form,$service);
                    break;
                default:
                    break;
            }
            
            $this->view->form->setMemberView();
        }
    }
    private function submitComment($form,$service)
    {
        $check = $form->getCheckReq($form->getValue('checkID'));
        $service->updateCheckReqComment($check->getComment(),$check->getId());
        
        return;
    }
    /**
     * Method handles treasuer view of a check request
     */
    public function checkreqAction()
    {
        $this->view->pageTitle = 'Check Request';
        
        $request = $this->getRequest();
        $service = new App_Service_TreasurerService();
                
        $check = $service->getCheckReqById($request->id);
        $this->view->form = new Application_Model_Treasurer_CheckForm($check);

        if(!$request->isPost()) {
                $this->view->form->setInitialButtons();
        }
        elseif($request->isPost() && $this->view->form->isValid($_POST)) {
            $this->view->form->populate($_POST);
            $action = $this->view->form->getAction();
            
            switch( $action )
            {
                case 'add_comment':
                    $this->view->form->setCommentState();
                    return;
                case 'submit_comment':
                    $this->submitComment($this->view->form,$service);
                    break;
                case 'edit_check':
                    $this->view->form->setEditState();
                    return;
                case 'submit_edits':
                    $this->submitEdits($this->view->form,$service);
                default:
                    break;
            }
            
            $this->view->form->setInitialButtons();
            //
            //$form->setMemberView();
            //if($request->denyCheck){
            //        $service->denyCheckRequest($request->checkID);
            //        $this->_helper->redirector('index');
            //}
            //if($request->editCheck === 'Edit Check Request'){
            //    $this->view->form->editCheckReq($request->editCheck);
            //}
            //if($request->editCheck === 'Submit Edits'){
            //        var_dump('this should persist');
            //        exit();
            //}
            //if($request->addComment){
            //        $this->view->form->addAComment($request->addComment);
            //}
            //        
            //return;
        }	
    }
    private function submitEdits($form,$service)
    {
        $check = $form->getCheckReq();
        $service->updateCheckRequest($check);
    }
    private function issueCheck()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $service->closeCheckRequest($identity->user_id, $request->checkID, $request->checkNum);
        $this->_helper->redirector('index');
    }
}
