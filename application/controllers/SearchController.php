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
        $this->_helper->authCheck(Application_Model_User::ROLE_MEMBER);
        $this->view->pageTitle = 'Client and Case Search';
    }

    public function treasurerAction()
    {
        $this->_helper->authCheck(Application_Model_User::ROLE_TREASURER);
        $this->view->pageTitle = 'Check Request Search';
    }
}
