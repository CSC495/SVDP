<?php
/**
 * Controller implementing the bulk of functionality needed by parish members.
 */
class MemberController extends Zend_Controller_Action
{

    /**
     * Home page action that lists open cases for the current member. Only a short summary shall be
     * displayed for each case.
     */
    public function indexAction()
    {
        $this->view->pageTitle = "Open Cases";

        $service           = new App_Service_Search();
        $userId            = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->view->cases = $service->getOpenCasesByUserId($userId);
    }
    
    public function clientAction()
    {
    	$this->view->pageTitle = 'Client View/Edit';
    	$this->view->form      = new Application_Model_ClientForm();

        if ($this->_hasParam('id')) {
            $service = new App_Service_Member();
            $client  = $service->getClientById($this->_getParam('id'));

            print_r($client);
        }
    }
    
    public function caseAction()
    {
    	$this->view->pageTitle = 'Case View/Edit';
    	$this->view->form      = new Application_Model_CaseForm();
    }
}
