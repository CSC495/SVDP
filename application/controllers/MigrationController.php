<?php

/**
 * Controller to allow members to migrate data to this new system.
 */
class MigrationController extends Zend_Controller_Action
{
    /**
     * Index of controller displays all forms needed to enter data
     */
    public function indexAction()
    {
        $request = $this->getRequest();
        
        $this->view->pageTitle = 'Migration Assistant';
        $this->view->form      = new Application_Model_Migration_MigrateForm();

        if ($this->validateForm()) {
            
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

        if (!$req->isGet() || (!$req->getQuery('search') && !$req->getQuery('listAll'))) {
            return false;
        }

        if (!$form->isValid($req->getQuery())) {
            foreach ($form->getMessages() as $elementErrors) {
                foreach ($elementErrors as $error) {
                    $this->_helper->flashMessenger(array('type' => 'error', 'text' => $error));
                }
            }
            return false;
        }

        return true;
    }
    
    /**
     * Action that allows members to add new clients or edit data about existing clients.
     */
    public function editclientAction()
    {
        $identity = Zend_Auth::getInstance()->getIdentity();
        $role     = $identity->role;

        $request = $this->getRequest();
        $service = new App_Service_Member();

        // A client is displayed read-only if the user is not a normal member (e.g., if they're a
        // treasurer).
        $readOnly = ($role === App_Roles::TREASURER);

        if ($this->_hasParam('id')) {
            // Editing an existing client.
            $id = $this->_getParam('id');

            $this->view->pageTitle = $readOnly ? 'View Client' : 'Edit Client';
            $this->view->form = new Application_Model_Member_ClientForm($id, $readOnly);

            if (!$request->isPost()) {
                // If the user hasn't submitted the form yet, load client info from the database.
                $this->view->form->setClient($service->getClientById($id));
                $this->view->form->setHouseholders($service->getHouseholdersByClientId($id));
                $this->view->form->setEmployers($service->getEmployersByClientId($id));
            }
        } else {
            // Adding a new client.
            if ($readOnly) {
                throw new DomainException('Only members can add new clients');
            }

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

        // Ensure that only members can edit clients.
        if ($readOnly) {
            throw new DomainException('Only members can edit existing clients');
        }

        // Re-add existing form data.
        $data = $request->getPost();

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
}
