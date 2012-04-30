<?php

class Application_Model_Member_ClientView extends Zend_Form
{
	private $_PARISH_OPTIONS = array(
        '' => '',
        'St. Raphael' => 'St. Raphael',
        'Holy Spirit' => 'Holy Spirit',
        'St. Elizabeth Seton' => 'St. Elizabeth Seton',
        'St. Thomas' => 'St. Thomas',
        'SS. Peter & Paul' => 'SS. Peter & Paul',
        'Other' => 'Other',
        'None' => 'None',
    );

    private $_id;

    private $_householdersSubForm;

    private $_employersSubForm;

    private static function makeActionUrl($id)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
        return $baseUrl->baseUrl(App_Resources::MEMBER . '/viewclient'
            . (($id !== null) ? '/id/' . urlencode($id) : ''));
    }

    public function __construct($id = null)
    {
        $this->_id = $id;
        $this->_householdersSubForm = new Zend_Form_SubForm();
        $this->_employersSubForm = new Zend_Form_SubForm();

        parent::__construct();

        $this
            ->addElementPrefixPath(
                'Twitter_Bootstrap_Form_Decorator',
                'Twitter/Bootstrap/Form/Decorator',
                'decorator'
            )
            ->setAction(self::makeActionUrl($id))
            ->setMethod('post')
            ->setDecorators(array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/client-view.phtml',
                    'householdersSubForm' => &$this->_householdersSubForm,
                    'employersSubForm' => &$this->_employersSubForm,
                )),
                array('Form', array('class' => 'member form-horizontal')),
            ))
            ->setElementDecorators(array(
                'FieldSize',
                'ViewHelper',
                'Addon',
                'ElementErrors',
                array('Description', array('class' => 'help-block')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'controls')),
                array('Label', array('class' => 'control-label')),
                'Wrapper',
            ));
        
        // Primary form actions:
        
        $this->addElement('submit', 'editClient', array(
        		'label' => 'Edit Client',
        		'decorators' => array('ViewHelper'),
        		'class' => 'btn btn-success',
        ));

        // Personal information elements:

        $this->addElement('text', 'firstName', array(
            'label' => 'First name',
            'maxlength' => 30,
            'dimension' => 3,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'lastName', array(
            'label' => 'Last name',
            'maxlength' => 30,
            'dimension' => 3,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'otherName', array(
            'label' => 'Other name',
            'description' => '(optional)',
            'maxlength' => 30,
            'dimension' => 3,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'birthDate', array(
            'label' => 'Birth date',
            'description' => '(optional)',
            'maxlength' => 10,
            'dimension' => 2,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'ssn4', array(
            'label' => 'Last four digits of SSN',
            'maxlength' => 4,
            'dimension' => 1,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('checkbox', 'married', array(
            'label' => 'Currently married',
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'spouseName', array(
            'label' => "Spouse's name",
            'maxlength' => 30,
            'dimension' => 3,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'spouseBirthDate', array(
            'label' => "Spouse's birth date",
            'description' => '(optional)',
            'maxlength' => 10,
            'dimension' => 2,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        // Additional information elements:

        $this->addElement('checkbox', 'doNotHelp', array(
            'label' => 'Do not help',
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'doNotHelpReason', array(
            'label' => '"Do not help" reason',
            'maxlength' => 100,
            'dimension' => 3,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('checkbox', 'veteran', array(
            'label' => 'Veteran',
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('select', 'parish', array(
            'label' => 'Parish attended',
            'dimension' => 3,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        // Contact information elements:

        $this->addElement('text', 'cellPhone', array(
            'label' => 'Cell phone',
            'maxlength' => 12,
            'dimension' => 2,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'homePhone', array(
            'label' => 'Home phone',
            'maxlength' => 12,
            'dimension' => 2,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'workPhone', array(
            'label' => 'Work phone',
            'maxlength' => 12,
            'dimension' => 2,
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addSubForm(new Application_Model_Member_AddressSubForm(null, true, true), 'addr');

        // Householder sub form and elements:

        $this->_householdersSubForm
            ->setDecorators(array('FormElements'))
            ->setElementDecorators(array('ViewHelper'));

        $this->addSubForm($this->_householdersSubForm, 'householders');

        $this->addElement('submit', 'newHouseholder', array(
            'label' => 'Add Another Member',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-info',
        ));

        // Employer sub form and elements:

        $this->_employersSubForm
            ->setDecorators(array('FormElements'))
            ->setElementDecorators(array('ViewHelper'));

        $this->addSubForm($this->_employersSubForm, 'employers');

        $this->addElement('submit', 'newEmployer', array(
            'label' => 'Add Another Employer',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-info',
        ));

        // Primary form actions:

        $this->addElement('submit', 'submit', array(
            'label' => ($id === null) ? 'Create Client' : 'Submit Changes',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));
    }

    public function isAddHouseholderRequest($data)
    {
        return isset($data['newHouseholder']);
    }

    public function isAddEmployerRequest($data)
    {
        return isset($data['newEmployer']);
    }

    public function prevalidate($data)
    {
        if (isset($data['married']) && $data['married']) {
            $this->spouseName->setRequired(true);
        }

        if (isset($data['householders'])) {
            foreach ($data['householders'] as $householderId => $householderData) {
                $this->_householdersSubForm->addSubForm(
                    new Application_Model_Member_HouseholderSubForm(),
                    $householderId
                );
            }
        }

        if (isset($data['employers'])) {
            foreach ($data['employers'] as $employerId => $employerData) {
                $this->_employersSubForm->addSubForm(
                    new Application_Model_Member_EmployerSubForm(),
                    $employerId
                );
            }
        }

        // XXX: This is kind of a hack, and it's slightly broken. We really should use a custom
        // validator class for our "at least one of these fields is required" logic.
        if (App_Formatting::isBlank($data['cellPhone'])
            && App_Formatting::isBlank($data['homePhone'])
            && App_Formatting::isBlank($data['workPhone'])) {
            $this->cellPhone->setRequired(true);
            $this->homePhone->setRequired(true);
            $this->workPhone->setRequired(true);
        }
    }

    public function getClient()
    {
        $client = new Application_Model_Impl_Client();
        $client->setId($this->_id)
               ->setFirstName(App_Formatting::emptyToNull($this->firstName->getValue()))
               ->setLastName(App_Formatting::emptyToNull($this->lastName->getValue()))
               ->setOtherName(App_Formatting::emptyToNull($this->otherName->getValue()))
               ->setMarried($this->married->isChecked())
               ->setBirthDate(App_Formatting::unformatDate($this->birthDate->getValue()))
               ->setSsn4(App_Formatting::emptyToNull($this->ssn4->getValue()))
               ->setCellPhone(App_Formatting::emptyToNull($this->cellPhone->getValue()))
               ->setHomePhone(App_Formatting::emptyToNull($this->homePhone->getValue()))
               ->setWorkPhone(App_Formatting::emptyToNull($this->workPhone->getValue()))
               ->setParish(App_Formatting::emptyToNull($this->parish->getValue()))
               ->setVeteran($this->veteran->isChecked())
               ->setCurrentAddr($this->addr->getAddr())
               ->setDoNotHelpReason($this->doNotHelp->isChecked()
                   ? App_Formatting::emptyToNull($this->doNotHelpReason->getValue())
                   : null);

        if ($client->isMarried()) {
            $spouse = new Application_Model_Impl_Client();
            $spouse->setFirstName(App_Formatting::emptyToNull($this->spouseName->getValue()))
                   ->setLastName($client->getLastName())
                   ->setMarried(true)
                   ->setBirthDate(App_Formatting::unformatDate($this->spouseBirthDate->getValue()))
                   ->setHomePhone($client->getHomePhone())
                   ->setParish($client->getParish());

            $client->setSpouse($spouse);
        }

        return $client;
    }

    public function setClient($client)
    {
        $this->firstName->setValue($client->getFirstName());
        $this->lastName->setValue($client->getLastName());
        $this->otherName->setValue($client->getOtherName());
        $this->married->setChecked($client->isMarried());
        $this->birthDate->setValue(App_Formatting::formatDate($client->getBirthDate()));
        $this->ssn4->setValue($client->getSsn4());
        $this->doNotHelp->setChecked($client->isDoNotHelp());
        $this->doNotHelpReason->setValue($client->getDoNotHelpReason());
        $this->veteran->setChecked($client->isVeteran());
        $this->parish->setValue($client->getParish());
        $this->homePhone->setValue($client->getFormattedHomePhone());
        $this->cellPhone->setValue($client->getFormattedCellPhone());
        $this->workPhone->setValue($client->getFormattedWorkPhone());
        $this->addr->setAddr($client->getCurrentAddr());

        if ($client->isMarried()) {
            $spouse = $client->getSpouse();
            $this->spouseName->setValue($spouse->getFirstName());
            $this->spouseBirthDate->setValue(App_Formatting::formatDate($spouse->getBirthDate()));
        }
    }

    public function getHouseholders()
    {
        $householders = array();

        foreach ($this->_householdersSubForm->getSubForms() as $householderSubForm) {
            $householders[] = $householderSubForm->getHouseholder();
        }

        return $householders;
    }

    public function setHouseholders($householders)
    {
        $this->_householdersSubForm->clearSubForms();

        $i = 0;

        foreach ($householders as $householder) {
            $householderSubForm = new Application_Model_Member_HouseholderSubForm();
            $householderSubForm->setHouseholder($householder);

            $this->_householdersSubForm->addSubForm($householderSubForm, $i++);
        }
    }

    public function addHouseholder()
    {
        $this->_householdersSubForm->addSubForm(
            new Application_Model_Member_HouseholderSubForm(),
            count($this->_householdersSubForm->getSubForms())
        );
    }

    public function getEmployers()
    {
        $employers = array();

        foreach ($this->_employersSubForm->getSubForms() as $employerSubForm) {
            $employers[] = $employerSubForm->getEmployer();
        }

        return $employers;
    }

    public function setEmployers($employers)
    {
        $this->_employersSubForm->clearSubForms();

        $i = 0;

        foreach ($employers as $employer) {
            $employerSubForm = new Application_Model_Member_EmployerSubForm();
            $employerSubForm->setEmployer($employer);

            $this->_employersSubForm->addSubForm($employerSubForm, $i++);
        }
    }

    public function addEmployer()
    {
        $this->_employersSubForm->addSubForm(
            new Application_Model_Member_EmployerSubForm(),
            count($this->_employersSubForm->getSubForms())
        );
    }
}
