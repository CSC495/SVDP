<?php
/**
 * Controller implementing the bulk of functionality needed by parish members.
 */
class MemberController extends Zend_Controller_Action
{

    /**
     * Home page action that allows members to locate potential clients on a map.
     */
    public function indexAction()
    {
        $this->view->pageTitle = 'Home';
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

        $firstName = $this->view->form->getFirstName();
        $lastName  = $this->view->form->getLastName();

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
                    'firstName' => $firstName,
                    'lastName' => $lastName,
                )
            );
        }

        // If we got this far, the address seems (vaguely) legit, and so we can get geocoding data.
        $mapService    = new App_Service_Map($this->view->form->getAddr());
        $searchService = new App_Service_Search();

        // Respond to geocoding errors.
        if ($mapService->hasErrorMsg()) {
            $this->_helper->flashMessenger(array(
                'type' => 'error',
                'text' => $mapService->getErrorMsg(),
            ));
            return;
        }

        if (!$mapService->hasResult()) {
            $this->_helper->flashMessenger(array(
                'type' => 'error',
                'text' => 'No results were found for that address.',
            ));
            return;
        }

        // Check for existing clients with similar address and/or names.
        $addr = $mapService->getAddr();

        $similarClients = $searchService->getSimilarClients($addr, $firstName, $lastName);

        if ($similarClients) {
            $this->_helper->flashMessenger(array(
                'text' => 'Clients with similar information were found.'
                       . ' <span id=map-similar-toggle>Check the list below.</span>',
                'noEscape' => true,
            ));
        }

        // Update the form with Google's reformatted address and prepare to show a Google map.
        $this->view->form->showNewClientButton();
        $this->view->form->setAddr($addr);
        $this->view->form->setSimilarClients($similarClients);
        $this->view->latitude  = $mapService->getLatitude();
        $this->view->longitude = $mapService->getLongitude();
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
     * Action that allows members to edit the parish schedule.
     */
    public function editscheduleAction()
    {
        $this->view->pageTitle = 'Edit Schedule';

        $request = $this->getRequest();
        $service = new App_Service_Member();
        $users   = $this->fetchMemberOptions($service);

        $this->view->form = new Application_Model_Member_ScheduleForm($users);

        if (!$request->isPost()) {
            // If this isn't a POST request, fill the form from existing entries.
            $this->view->form->setEntries(Zend_Registry::get('schedule'));
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
     * Action that lists contacts for parish members.
     */
    public function contactsAction()
    {
        $this->view->pageTitle = 'Member Contact List';

        $service = new App_Service_AdminService();
        $users   = $service->getAllUsers();

        $this->view->users = array();
        $lastRowLetter     = null;

        foreach ($users as $userId => $user) {
            $firstName = $user->getFirstName();

            if ($lastRowLetter !== $firstName[0]) {
                $lastRowLetter = $rowLetter = $firstName[0];
            } else {
                $rowLetter = null;
            }

            $this->view->users[$userId] = array(
                'user' => $user,
                'rowLetter' => $rowLetter,
            );
        }
    }

    /**
     * Action that shows a landing page for the given client.
     */
    public function viewclientAction()
    {
        // If no ID was provided, bail out.
        if (!$this->_hasParam('id')) {
            throw new UnexpectedValueException('No ID parameter provided');
        }

        // Fetch client data for display.
        $userId = Zend_Auth::getInstance()->getIdentity()->user_id;

        $memberService = new App_Service_Member();
        $searchService = new App_Service_Search();

        $client   = $memberService->getClientById($this->_getParam('id'));
        $cases    = $searchService->getCasesByClientId($client->getId());
        $comments = $memberService->getCommentsByClientId($client->getId());

        // Initialize the client view form.
        $this->view->pageTitle = 'View Client';
        $this->view->form      = new Application_Model_Member_ViewClientForm(
            $userId, $client, $cases, $comments);

        // If this isn't a POST request or form validation fails, bail out.
        $request = $this->getRequest();

        if (!$request->isPost() || !$this->view->form->isValid($request->getPost())) {
            return;
        }

        // Handle requests to add client comments.
        $comment = $this->view->form->getAddedComment($request->getPost());

        if ($comment !== null) {
            $memberService->createClientComment($client->getId(), $comment);
        }

        // Redirect back to view client action to display updated case data.
        $this->_helper->redirector('viewClient', App_Resources::MEMBER, null, array(
            'id' => $client->getId(),
        ));
    }

    /**
     * Action that shows a landing page for the given case.
     */
    public function viewcaseAction()
    {
        // If no ID was provided, bail out.
        if (!$this->_hasParam('id')) {
            throw new UnexpectedValueException('No ID parameter provided');
        }

        // Fetch client data for display.
        $userId = Zend_Auth::getInstance()->getIdentity()->user_id;

        $service = new App_Service_Member();

        $case     = $service->getCaseById($this->_getParam('id'));
        $comments = $service->getCommentsByCaseId($case->getId());
        $users    = $this->fetchMemberOptions($service);

        // Initialize the case view form.
        $this->view->pageTitle = 'View Case';
        $this->view->form      = new Application_Model_Member_ViewCaseForm(
            $userId, $case, $comments, $users);

        // If this isn't a POST request, populate the form from the database and bail out.
        $request = $this->getRequest();

        if (!$request->isPost()) {
            $this->view->form->setNeeds($case->getNeeds());
            $this->view->form->setVisits($case->getVisits());
            return;
        }

        // Repopulate the form with POST data.
        $data = $request->getPost();
        $this->view->form->preValidate($data);
        $this->view->form->populate($data);

        if (!$this->view->form->isChangeNeedsRequest($data)) {
            $this->view->form->setNeeds($case->getNeeds());
        }

        if (!$this->view->form->isChangeVisitsRequest($data)) {
            $this->view->form->setVisits($case->getVisits());
        }

        // If the user is adding or removing needs/visits or form validation fails, bail out.
        if ($this->view->form->handleAddRemoveRecords($data)
            || !$this->view->form->isValid($data)) {
            return;
        }

        // Handle requests to close the case.
        if ($this->view->form->isCloseCaseRequest($data)) {
            $service->closeCaseById($case->getId());
        }

        // Handle requests to add, edit, and/or remove case needs.
        if ($this->view->form->isChangeNeedsRequest($data)) {
            $changedNeeds = $this->view->form->getChangedNeeds();
            $removedNeeds = $this->view->form->getRemovedNeeds();

            if (count($removedNeeds) - count($changedNeeds) === count($case->getNeeds())) {
                $this->_helper->flashMessenger(array(
                    'type' => 'error',
                    'text' => 'A case must have at least one need.',
                ));
                return;
            }

            foreach ($changedNeeds as $changedNeed) {
                $service->changeCaseNeed($case->getId(), $changedNeed);
            }
            $service->removeCaseNeeds($removedNeeds);
        }

        // Handle requests to add, edit, and/or remove case visits.
        if ($this->view->form->isChangeVisitsRequest($data)) {
            foreach ($this->view->form->getChangedVisits() as $changedVisit) {
                $service->changeCaseVisit($case->getId(), $changedVisit);
            }
            $service->removeCaseVisits($this->view->form->getRemovedVisits());
        }

        // Handle requests to add case comments.
        $comment = $this->view->form->getAddedComment($data);

        if ($comment !== null) {
            $service->createCaseComment($case->getId(), $comment);
        }

        // Redirect back to view case action to display updated case data.
        $this->_helper->redirector('viewCase', App_Resources::MEMBER, null, array(
            'id' => $case->getId(),
        ));
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

            $client = new Application_Model_Impl_Client();

            // Possibly using name information from map action.
            $client
                ->setFirstName(App_Formatting::emptyToNull($this->_getParam('firstName')))
                ->setLastName(App_Formatting::emptyToNull($this->_getParam('lastName')));

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

                $client->setCurrentAddr($addr);
            }

            $this->view->form->setClient($client);
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
        $client = $this->view->form->getClient();

        $changedHouseholders = $this->view->form->getChangedHouseholders();
        $changedEmployers    = $this->view->form->getChangedEmployers();

        $user = new Application_Model_Impl_User();
        $user->setUserId(Zend_Auth::getInstance()->getIdentity()->user_id);

        if ($this->_hasParam('id')) {
            // Update an existing client.
            $removedHouseholders = $this->view->form->getRemovedHouseholders();
            $removedEmployers    = $this->view->form->getRemovedEmployers();

            if ($this->view->form->isMaritalStatusChange() && $client->isMarried()) {
                // If an existing client gets married, then we need to track the creation date and
                // creating user for the newly entered spouse.
                $client->getSpouse()
                    ->setUser($user)
                    ->setCreatedDate(date('Y-m-d'));
            }

            $client = $service->editClient(
                $client,
                $changedHouseholders,
                $changedEmployers,
                $removedHouseholders,
                $removedEmployers,
                $this->view->form->isMove(),
                $this->view->form->isMaritalStatusChange()
            );
        } else {
            // Add a new client.
            $client
                ->setUser($user)
                ->setCreatedDate(date('Y-m-d'));

            if ($client->isMarried()) {
                $client->getSpouse()
                    ->setUser($user)
                    ->setCreatedDate(date('Y-m-d'));
            }

            $client = $service->createClient($client, $changedHouseholders, $changedEmployers);
        }

        $this->_helper->redirector('viewClient', App_Resources::MEMBER, null, array(
            'id' => $client->getId(),
        ));
    }

	public function clienthistoryAction()
	{
		// If no client ID was provided, bail out.
        if (!$this->_hasParam('id')) {
            throw new UnexpectedValueException('No client ID parameter provided');
        }

        // Initialize the new client history form.
        $service = new App_Service_Member();
        $client  = $service->getClientById($this->_getParam('id'));
		
		
		$this->view->pageTitle = 'View Household History';
		$this->view->client    = $client;
		$this->view->form      = new Application_Model_Member_CaseForm($client->getId());
		
	}


    /**
     * Action that allows members to add new cases.
     */
    public function newcaseAction()
    {
        // If no client ID was provided, bail out.
        if (!$this->_hasParam('clientId')) {
            throw new UnexpectedValueException('No client ID parameter provided');
        }

        // Initialize the new case form.
        $service = new App_Service_Member();
        $client  = $service->getClientById($this->_getParam('clientId'));

        $this->view->pageTitle = 'New Case';
        $this->view->client    = $client;
        $this->view->form      = new Application_Model_Member_CaseForm($client->getId());

        // If this isn't a post request, then we're done.
        $request = $this->getRequest();

        if (!$request->isPost()) {
            // Since there must always be at least one need for any given case, help the user out by
            // adding a blank need.
            $this->view->form->addEmptyNeed();
            return;
        }

        // Re-add existing form data.
        $data = $request->getPost();

        $this->view->form->preValidate($data);
        $this->view->form->populate($data);

        // If the user just submitted the form, make some validation goodness happen.
        if ($this->view->form->handleAddRemoveNeeds($data) || !$this->view->form->isValid($data)) {
            return;
        }

        // If we passed validation, try and get the needs for the new case.
        $needs = $this->view->form->getChangedNeeds();

        if (!$needs) {
            $this->_helper->flashMessenger(array(
                'type' => 'error',
                'text' => 'You must add at least one case need.',
            ));
            return;
        }

        // Add the new case to the database and redirect to the new case's view page.
        $user = new Application_Model_Impl_User();
        $user->setUserId(Zend_Auth::getInstance()->getIdentity()->user_id);

        $case = new Application_Model_Impl_Case();
        $case
            ->setClient($client)
            ->setOpenedUser($user)
            ->setOpenedDate(date('Y-m-d'))
            ->setStatus('Open')
            ->setNeeds($needs);

        $case = $service->createCase($case);

        $this->_helper->redirector('viewCase', App_Resources::MEMBER, null, array(
            'id' => $case->getId(),
        ));
    }

    /**
     * Action allowing members to add referrals to unprocessed case needs.
     */
    public function newreferralAction()
    {
        // If no case ID or case need ID was provided, bail out.
        if (!$this->_hasParam('caseId')) {
            throw new UnexpectedValueException('No case ID parameter provided');
        }

        if (!$this->_hasParam('needId')) {
            throw new UnexpectedValueException('No case need ID parameter provided');
        }

        // Get information on the case associated with this new referral.
        $service = new App_Service_Member();
        $case    = $service->getCaseById($this->_getParam('caseId'));

        // Create the referral form.
        $needId                = $this->_getParam('needId');
        $this->view->pageTitle = 'New Referral';
        $this->view->case      = $case;
        $this->view->form      = new Application_Model_Member_ReferralForm($case, $needId);

        // If this isn't a POST request or form validation fails, bail out.
        $request = $this->getRequest();

        if (!$request->isPost() || !$this->view->form->isValid($request->getPost())) {
            return;
        }

        // If everyone's kosher with the form, then we can add the referral and redirect back to the
        // case view page.
        $referral = $this->view->form->getReferral();
        $referral->setDate(date('Y-m-d'));

        $service = new App_Service_Member();
        $service->createReferral($needId, $referral);

        $this->_helper->redirector('viewCase', App_Resources::MEMBER, null, array(
            'id' => $case->getId(),
        ));
    }

    /**
     * Action allowing members to open check requests for unprocessed case needs.
     */
    public function newcheckreqAction()
    {
        // If no case ID or case need ID was provided, bail out.
        if (!$this->_hasParam('caseId')) {
            throw new UnexpectedValueException('No case ID parameter provided');
        }

        if (!$this->_hasParam('needId')) {
            throw new UnexpectedValueException('No case need ID parameter provided');
        }

        // Get information on the case associated with this new check request.
        $service = new App_Service_Member();
        $case    = $service->getCaseById($this->_getParam('caseId'));

        // Create the check request form.
        $needId                = $this->_getParam('needId');
        $this->view->pageTitle = 'New Check Request';
        $this->view->case      = $case;
        $this->view->form      = new Application_Model_Member_CheckReqForm($case, $needId);

        // If this isn't a POST request or form validation fails, bail out.
        $request = $this->getRequest();

        if (!$request->isPost()) {
            return;
        }

        if (!$this->view->form->isValid($request->getPost())) {
            return;
        }

        // If everyone's kosher with the form, then we can add the check request and redirect back
        // to the case view page.
        $user = new Application_Model_Impl_User();
        $user->setUserId(Zend_Auth::getInstance()->getIdentity()->user_id);

        $checkReq = $this->view->form->getCheckReq();
        $checkReq
            ->setCaseNeedId($needId)
            ->setUser($user)
            ->setRequestDate(date('Y-m-d'))
            ->setStatus('P');

        $service->createCheckRequest($checkReq);

        $this->_helper->redirector('viewCase', App_Resources::MEMBER, null, array(
            'id' => $case->getId(),
        ));
    }

    private function fetchMemberOptions(App_Service_Member $service)
    {
        $users = $service->getActiveMembers();

        foreach ($users as &$user) {
            $user = $user->getFirstName() . ' ' . $user->getLastName();
        }

        return array('' => '') + $users;
    }
}
