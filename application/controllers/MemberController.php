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

            $this->prefillClient($this->view->form, $client);
        }
    }
    
    public function caseAction()
    {
    	$this->view->pageTitle = 'Case View/Edit';
    	$this->view->form      = new Application_Model_CaseForm();
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
        $form->address->setValue($addr->getStreet());
        $form->apartment->setValue($addr->getApt());
        $form->city->setValue($addr->getCity());
        $form->state->setValue($addr->getState());
        $form->zipcode->setValue($addr->getZip());
        $form->marriageStatus->setChecked($client->isMarried());
        $form->birthdate->setValue($client->getBirthDate());
        $form->ssn4->setValue($client->getSsn4());
        $form->veteranFlag->setChecked($client->isVeteran());
        $form->memberParish->setValue($client->getParish());
        $form->createdDate->setValue($client->getCreatedDate());
        $form->createdUser->setValue($client->getUserId());
    }
}
