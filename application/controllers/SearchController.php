<?php

class SearchController extends Zend_Controller_Action
{

    public function init()
    {
    }

    public function indexAction()
    {
        // Redirect the index action to the member search page as that's what end users will likely
        // want if they manually navigate there.
        $this->_helper->redirector('member');
    }

    public function memberAction()
    {
        $this->view->pageTitle = 'Client and Case Search';
        $this->view->form      = new Application_Model_MemberSearchForm();
    }

    public function treasurerAction()
    {
        $this->view->pageTitle = 'Check Request Search';
        $this->view->form      = new Application_Model_TreasurerSearchForm();
    }
}
