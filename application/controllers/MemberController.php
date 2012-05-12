<?php
/**
 * Controller implementing the bulk of functionality needed by parish members.
 */
class MemberController extends Zend_Controller_Action
{

    /**
     * Home page action: just redirects to the map search screen.
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
        $this->view->form = new Application_Model_Member_MapForm();

        // Load the Google Maps JavaScript API.
        $this->view->headScript()->appendFile(
            'http://maps.googleapis.com/maps/api/js?sensor=false&libraries=geometry');

        // If we don't have any GET parameters, display the form but don't look up any coordinates.
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
            $addr = $this->view->form->getAddr();

            $this->_helper->redirector(
                'editClient',
                App_Resources::MEMBER,
                null,
                array(
                    'street' => $addr->getStreet(),
                    'apt' => $addr->getApt(),
                    'city' => $addr->getCity(),
                    'state' => $addr->getState(),
                    'zip' => $addr->getZip(),
                    'parish' => $addr->getParish(),
                )
            );
        }

        // If we got this far, the address seems (vaguely) legit, and so we can get geocoding data.
        $service = new App_Service_Map($this->view->form->getAddr());

        // Respond to geocoding errors.
        if ($service->hasErrorMsg()) {
            $this->_helper->flashMessenger($service->getErrorMsg());
            return;
        }

        if (!$service->hasResult()) {
            $this->_helper->flashMessenger('No results were found for that address.');
            return;
        }

        // Update the form with Google's reformatted address and prepare to show a Google map.
        $this->view->form->showNewClientButton();
        $this->view->form->setAddr($service->getAddr());
        $this->view->latitude = $service->getLatitude();
        $this->view->longitude = $service->getLongitude();
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

    /**
     * Action that lists contacts for parish members.
     */
    public function contactsAction()
    {
        $this->view->pageTitle = 'Member Contact List';

        $service = new App_Service_AdminService();

        $this->view->users = $service->getAllUsers();
    }

    /**
     * Action that allows members to edit the parish schedule.
     */
    public function editscheduleAction()
    {
        $this->view->pageTitle = 'Edit Schedule';

        $request = $this->getRequest();
        $service = new App_Service_Member();

        $users = $service->getActiveMembers();

        foreach ($users as &$user) {
            $user = $user->getFirstName() . ' ' . $user->getLastName();
        }
        unset($user);

        $this->view->form = new Application_Model_Member_ScheduleForm(array('' => '') + $users);

        if (!$request->isPost()) {
            // If this isn't a POST request, fill the form from existing entries.
            $this->view->form->setEntries($service->getScheduleEntries());
            return;
        }

        // Repopulate the form with POST data.
        $data = $request->getPost();
        $this->view->form->preValidate($data);
        $this->view->form->populate($data);

        if ($this->view->form->handleAddRemoveEntries($data)
                || !$this->view->form->isValid($data)) {
            // If the user just added or removed a schedule entry, then we're done. Do likewise for
            // validation errors.
            return;
        }

        // Handle added, modified, and deleted schedule entries.
        foreach ($this->view->form->getChangedEntries() as $changedEntry) {
            $service->changeScheduleEntry($changedEntry);
        }
        $service->removeScheduleEntries($this->view->form->getRemovedEntries());
        $this->_helper->redirector('editSchedule');
    }

    /**
     * Action that allows members to add new clients or edit data about existing clients.
     */
    public function editclientAction()
    {
        $request = $this->getRequest();
        $service = new App_Service_Member();

        if ($this->_hasParam('id')) {
            // Editing an existing client.
            $id = $this->_getParam('id');

            $this->view->pageTitle = 'Edit Client';
            $this->view->form = new Application_Model_Member_ClientForm($id);

            if (!$request->isPost()) {
                // If the user hasn't submitted the form yet, load client info from the database.
                $this->view->form->setClient($service->getClientById($id));
                $this->view->form->setHouseholders($service->getHouseholdersByClientId($id));
                $this->view->form->setEmployers($service->getEmployersByClientId($id));
            }
        } else {
            // Adding a new client.
            $this->view->pageTitle = 'New Client';
            $this->view->form = new Application_Model_Member_ClientForm();

            if (!$request->isPost() && $this->_hasParam('street') && $this->_hasParam('city')
                    && $this->_hasParam('state')) {
                // Using address information from map action.
                $addr = new Application_Model_Impl_Addr();
                $addr
                    ->setStreet($this->_getParam('street'))
                    ->setApt(App_Formatting::emptyToNull($this->_getParam('apt')))
                    ->setCity($this->_getParam('city'))
                    ->setState($this->_getParam('state'))
                    ->setZip(App_Formatting::emptyToNull($this->_getParam('zip')))
                    ->setParish(App_Formatting::emptyToNull($this->_getParam('parish')));

                $client = new Application_Model_Impl_Client();
                $client->setCurrentAddr($addr);

                $this->view->form->setClient($client);
            }
        }

        // If this isn't a post request, then we're done.
        if (!$request->isPost()) {
            return;
        }

        $data = $request->getPost();

        // Re-add existing form data.
        $this->view->form->preValidate($data);
        $this->view->form->populate($data);

        // If the user just submitted the form, make some validation goodness happen.
        // If the user requested that we add or remove a household member or employer, or if form
        // validation failed, then we're done here.
        if ($this->view->form->handleAddRemoveRecords($data)
                || !$this->view->form->isValid($data)) {
            return;
        }

        // If we passed validation, insert or update the database as required.
        if ($this->_hasParam('id')) {
            // TODO: Update existing client.
        } else {
            $client       = $this->view->form->getClient();
            $householders = $this->view->form->getChangedHouseholders();
            $employers    = $this->view->form->getChangedEmployers();

            $client
                ->setUserId(Zend_Auth::getInstance()->getIdentity()->user_id)
                ->setCreatedDate(date('Y-m-d'));

            if ($client->isMarried()) {
                $client->getSpouse()
                    ->setUserId(Zend_Auth::getInstance()->getIdentity()->user_id)
                    ->setCreatedDate(date('Y-m-d'));
            }

            $client = $service->createClient($client, $householders, $employers);

            $this->_helper->redirector('editClient', App_Resources::MEMBER, null, array(
                'id' => $client->getId(),
            ));
        }
    }

    /**
     * Action that allows members to add new cases or edit data about existing cases.
     */
    public function editcaseAction()
    {
    	$this->view->pageTitle = 'Case View/Edit';
    	$this->view->form      = new Application_Model_Member_CaseForm();
    }
}
