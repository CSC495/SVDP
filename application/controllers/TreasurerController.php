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
        $actionUrl = $baseUrl->baseUrl(App_Resources::TREASURER) . '/view';
        $this->view->form->setAction($actionUrl);
            
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
                case 'cancel_comment':
                    $this->view->form = new Application_Model_Treasurer_CheckForm($check);
		    $this->view->form->setAction($actionUrl);
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
	
	// Check if there is param
        if( !$request->id )
            $this->_helper->redirector('index');
            
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
                    break;
                case 'deny_check':
                    $this->denyCheck($this->view->form,$service);
                    return;
                case 'issue_check':
                    $this->issueCheck($this->view->form,$service);
                    return;
                case 'cancel_edits':
                case 'cancel_comment':
                    $this->view->form = new Application_Model_Treasurer_CheckForm($check);
                    break;
                default:
                    break;
            }
            
            $this->view->form->setInitialButtons();
        }
        elseif(!$this->view->form->isValid($_POST)){
            $action = $this->view->form->getAction();
            // reset form if user wanted to cancel
            if( $action === 'cancel_edits' ){
                $this->view->form = new Application_Model_Treasurer_CheckForm($check);
                return $this->view->form->setInitialButtons();
            }
            //@TODO it may be bad to assume we are in edit mode if there
            // is something wrong?...
            $this->view->form->setEditState();
        }
    }
    private function denyCheck($form,$service)
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        
        $check = $form->getCheckReq();
        $service->denyCheckRequest($check->getId(),$identity->user_id);
        
        return $this->_forward('index', App_Resources::REDIRECT, null,
                        Array( 'msg' => 'Check Request Has Been Denied!',
                               'time' => 2,
                               'controller' => App_Resources::TREASURER,
                               'action' => 'index')); 
    }
    private function submitEdits($form,$service)
    {
        $check = $form->getCheckReq();
        $service->updateCheckRequest($check);
    }
    private function issueCheck($form,$service)
    {
        $checkNum = $form->checkNum;
        $checkNum->setRequired(true);
        // Ensure check number provided
        if( !$checkNum->isValid( $checkNum->getValue()) ){
            $form->setInitialButtons();
            return;
        }

        $identity = Zend_Auth::getInstance()->getIdentity();
        $check = $form->getCheckReq();
        $service->closeCheckRequest($identity->user_id, $check->getId(), $check->getCheckNumber());
        
        return $this->_forward('index', App_Resources::REDIRECT, null,
                        Array( 'msg' => 'Check Request Has Been Issued!',
                               'time' => 2,
                               'controller' => App_Resources::TREASURER,
                               'action' => 'index')); 
    }
}
