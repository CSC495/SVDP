<?php
/**
 * Controller implementing the bulk of functionality needed by parish members.
 */
class MemberController extends Zend_Controller_Action
{

    /**
     * Home page action: currently just redirects to the map search screen.
     */
    public function indexAction()
    {
        $this->_helper->redirector('map');
    }

    /**
     * Action that allows members to locate potential clients on a map.
     */
    public function mapAction()
    {
        $this->view->pageTitle = 'Maps';
        $this->view->form = new Application_Model_Member_MapsForm();

        // If we don't have any GET parameters, display the form but not the map.
        $request = $this->getRequest();

        if (!$request->getQuery('search') && !$request->getQuery('newClient')) {
            return;
        }

        // Otherwise, check for form errors.
        if (!$this->view->form->isValid($request->getQuery())) {
            return;
        }

        // If the user wants to create a new client, redirect them to the appropriate place.
        if ($this->view->form->isNewClientRequest()) {
            $this->_helper->redirector('editclient', App_Resources::MEMBER, null,
                $this->view->form->getValues());
        }

        // If we got this far, the address seems (vaguely) legit, and so we can fetch geolocation
        // data.
        $service = new App_Service_Map($this->view->form->getAddr());

        // Respond to geocoding errors.
        if ($service->hasErrorMsg()) {
            $this->_helper->flashMessenger($service->getErrorMsg());
            return;
        } else if (!$service->hasResult()) {
            $this->_helper->flashMessenger('No results were found for that address.');
            return;
        }

        // Update the form with Google's reformatted address and prepare to show a Google map.
        $this->view->form->showNewClientButton();
        $this->view->form->setAddr($service->getAddr());
        $this->view->latitude = $service->getLatitude();
        $this->view->longitude = $service->getLongitude();

        $this->view->headScript()->appendFile(
            'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry');
    }

    /**
     * Action that lists open cases for the current member. Only a short summary shall be
     * displayed for each case.
     */
    public function opencasesAction()
    {
        $this->view->pageTitle = 'Open Cases';

        $service           = new App_Service_Search();
        $userId            = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->view->cases = $service->getOpenCasesByUserId($userId);
    }

    public function clientAction()
    {
    	$this->view->pageTitle = 'Client View/Edit';
    	$this->view->form      = new Application_Model_Member_ClientForm();

        if ($this->_hasParam('id')) {
            $service = new App_Service_Member();
            $client  = $service->getClientById($this->_getParam('id'));

            $this->prefillClient($this->view->form, $client);
        }
    }

    public function caseAction()
    {
    	$this->view->pageTitle = 'Case View/Edit';
    	$this->view->form      = new Application_Model_Member_CaseForm();
    }

    private function prefillClient($form, $client)
    {
        $addr = $client->getCurrentAddr();

        $form->clientID->setValue($client->getId());
        $form->firstName->setValue($client->getFirstName());
        $form->lastName->setValue($client->getLastName());
        $form->otherName->setValue($client->getOtherName());
        $form->doNotHelp->setChecked($client->isDoNotHelp());
        $form->homePhone->setValue($client->getFormattedHomePhone());
        $form->cellPhone->setValue($client->getFormattedCellPhone());
        $form->workPhone->setValue($client->getFormattedWorkPhone());
        $form->street->setValue($addr->getStreet());
        $form->apartment->setValue($addr->getApt());
        $form->city->setValue($addr->getCity());
        $form->state->setValue($addr->getState());
        $form->zipcode->setValue($addr->getZip());
        //$form->marriageStatus->setChecked($client->isMarried());
        $form->birthdate->setValue($client->getBirthDate());
        $form->ssn4->setValue($client->getSsn4());
        $form->veteranFlag->setChecked($client->isVeteran());
        $form->memberParish->setValue($client->getParish());
        $form->createdDate->setValue($client->getCreatedDate());
        $form->createdUser->setValue($client->getUserId());
    }
}
