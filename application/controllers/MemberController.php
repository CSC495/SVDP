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
	$memberService = new App_Service_Member();
	$client = new Application_Model_Impl_Client();
	$spouse = new Application_Model_Impl_Client();
	$address = new Application_Model_Impl_Addr();
	//TEST CLIENT
	$client->setUserId('asmith');
	$client->setId('1');
		$client->setFirstName('TestFirst');
		$client->setLastName('TestLast');
		$client->setOtherName('TestOther');
		$client->setMarried('0');
		$client->setBirthDate('2000-12-12');
		$client->setSsn4('3333');
		$client->setCellPhone('5555555555');
		$client->setHomePhone('4444444444');
		$client->setWorkPhone('3333333333');
		$client->setCreatedDate('2012-4-28');
		$client->setParish('St.Vincent DePaul');
		$client->setVeteran('1');
		
		$spouse->setFirstName('TESTspouseFIRST');
		$spouse->setLastName('TESTspouseLAST');
		
		$address->setStreet('123 TestStreetName');
		$address->setCity('Test');
		$address->setState('IL');
		$address->setZip('60540');
		$address->setParish('St. Vincint DePaul');
		$client->setAddress($address);
		$client->setSpouse($spouse);
	$memberService->editClient($client, '1', '1');
        $this->_helper->redirector('map');
    }

    /**
     * Action that allows members to locate potential clients on a map.
     */
    public function mapAction()
    {
        $this->view->pageTitle = 'Maps';
        $this->view->form = new Application_Model_Member_MapForm();

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
                )
            );
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
                $addr->setStreet($this->_getParam('street'))
                     ->setApt($this->_getParam('apt') !== '' ? $this->_getParam('apt') : null)
                     ->setCity($this->_getParam('city'))
                     ->setState($this->_getParam('state'))
                     ->setZip($this->_getParam('zip') !== '' ? $this->_getParam('zip') : null);

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

        // Handles requests to add new householders or employers.
        if ($this->view->form->isAddHouseholderRequest($data)) {
            $this->view->form->addHouseholder();
            return;
        }

        if ($this->view->form->isAddEmployerRequest($data)) {
            $this->view->form->addEmployer();
            return;
        }

        // If the user just submitted the form, make some validation goodness happen.
        if (!$this->view->form->isValid($data)) {
            return;
        }

        // If we passed validation, insert or update the database as required.
        if ($this->_hasParam('id')) {
            // TODO: Update existing client.
        } else {
            $client       = $this->view->form->getClient();
            $householders = $this->view->form->getHouseholders();
            $employers    = $this->view->form->getEmployers();

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

    public function contactsAction()
    {
        $this->view->pageTitle = 'Member Contact List';

        // TODO: Eventually we should specialize this query to only list active members. (But should
        // the specialized version go into the admin service or the member service?)
        $service = new App_Service_AdminService();

        $this->view->users = $service->getParishMembers();
    }
}
