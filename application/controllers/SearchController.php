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

        $req  = $this->getRequest();
        $form = $this->view->form;

        if (!$req->isGet() || !$req->getQuery('search')) {
            return;
        }

        if (!$form->isValid($req->getQuery())) {
            foreach ($form->getMessages() as $elementErrors) {
                foreach ($elementErrors as $error) {
                    $this->_helper->flashMessenger($error);
                }
            }
            return;
        }
    }

    public function treasurerAction()
    {
        $this->view->pageTitle = 'Check Request Search';
        $this->view->form      = new Application_Model_MemberSearchForm();

        $req  = $this->getRequest();
        $form = $this->view->form;

        if (!$req->isGet() || !$req->getQuery('search')) {
            return;
        }

        if (!$form->isValid($req->getQuery())) {
            foreach ($form->getMessages() as $elementErrors) {
                foreach ($elementErrors as $error) {
                    $this->_helper->flashMessenger($error);
                }
            }
            return;
        }
    }
}
