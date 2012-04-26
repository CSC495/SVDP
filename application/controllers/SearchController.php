<?php

/**
 * Controller to handle search requests made by member and treasurer users.
 */
class SearchController extends Zend_Controller_Action
{

    /**
     * Main search action should never be linked to, but if someone access it manually, then we'll
     * send them to member search page rather than just 404ing.
     */
    public function indexAction()
    {
        // Redirect the index action to the member search page as that's what end users will likely
        // want if they manually navigate there.
        $this->_helper->redirector('member');
    }

    /**
     * Action that displays member search form and executes client search queries.
     */
    public function memberAction()
    {
        $this->view->pageTitle = 'Client and Case Search';
        $this->view->form      = new Application_Model_Search_SearchFormMember();

        if ($this->validateForm()) {
            $searchType  = $this->view->form->getType();
            $searchQuery = $this->view->form->getQuery();

            switch ($searchType) {
                // Member searches by client ID go to a single client's page.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_ID:
                    $this->_helper->redirector('client', App_Resources::MEMBER, null, array(
                        'id' => $searchQuery,
                    ));

                // Member searches by case ID go to a single case's page.
                case Application_Model_Search_SearchFormAbstract::TYPE_CASE_ID:
                    $this->_helper->redirector('case', App_Resources::MEMBER, null, array(
                        'id' => $searchQuery,
                    ));
            }

            $service = new App_Service_Search();

            switch ($searchType) {
                // Member searches by client name retrieve a list of clients.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_NAME:
                    $this->view->clients = $service->getClientsByName($searchQuery);
                    break;

                // Member searches by client address retrieve a list of clients.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_ADDR:
                    $this->view->clients = $service->getClientsByAddr($searchQuery);
                    break;

                // Member searches by client phone number retrieve a list of clients.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_PHONE:
                    $this->view->clients = $service->getClientsByPhone($searchQuery);
                    break;
            }
        }
    }

    /**
     * Action that displays treasurer search form and executes check request search queries.
     */
    public function treasurerAction()
    {
        $this->view->pageTitle = 'Check Request Search';
        $this->view->form      = new Application_Model_Search_SearchFormTreasurer();

        if ($this->validateForm()) {
            $searchType  = $this->view->form->getType();
            $searchQuery = $this->view->form->getQuery();

            switch ($searchType) {
                // Treasurer searches by check request go to a single check request's page.
                case Application_Model_Search_SearchFormAbstract::TYPE_CHECK_REQ_ID:
                    $this->_helper->redirector('case', App_Resources::MEMBER, null, array(
                        'id' => $this->view->form->getQuery(),
                    ));
            }

            $service = new App_Service_Search();

            switch ($searchType) {
                // Treasurer searches by client name retrieve a list of check requests.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_NAME:
                    $this->view->checkReqs = $service->getCheckReqsByClientName($searchQuery);
                    break;

                // Treasurer searches by client address retrieve a list of check requests.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_ADDR:
                    $this->view->checkReqs = $service->getCheckReqsByClientAddr($searchQuery);
                    break;

                // Treasurer searches by client phone number retrieve a list of check requests.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_PHONE:
                    $this->view->checkReqs = $service->getCheckReqsByClientPhone($searchQuery);
                    break;

                // Treasurer searches by client ID retrieve a list of check requests.
                case Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_ID:
                    $this->view->checkReqs = $service->getCheckReqsByClientId($searchQuery);
                    break;

                // Treasurer searches by case ID retrieve a list of check requests.
                case Application_Model_Search_SearchFormAbstract::TYPE_CASE_ID:
                    $this->view->checkReqs = $service->getCheckReqsByCaseId($searchQuery);
                    break;
            }
        }
    }

    /**
     * Validate the search form stored in `$this->view->form`, displaying any errors as flash
     * messages. If there are no GET arguments associated with the current request, then no action
     * is taken.
     *
     * @return bool `true` if the user sent a GET request with valid data, `false` if there are no
     * GET arguments or a field is invalid.
     */
    private function validateForm()
    {
        $req  = $this->getRequest();
        $form = $this->view->form;

        if (!$req->isGet() || !$req->getQuery('search')) {
            return false;
        }

        if (!$form->isValid($req->getQuery())) {
            foreach ($form->getMessages() as $elementErrors) {
                foreach ($elementErrors as $error) {
                    $this->_helper->flashMessenger($error);
                }
            }
            return false;
        }

        return true;
    }
}
