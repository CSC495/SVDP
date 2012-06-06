<?php

class Application_Model_Member_ClientForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_MARRIAGE_OPTIONS = array(
        '' => '',
        'Single' => 'Single',
        'Married' => 'Married',
        'Divorced' => 'Divorced',
        'Separated' => 'Separated',
        'Other' => 'Other',
    );

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

    private $_CHANGE_TYPE_OPTIONS = array(
        '' => '',
        'move' => 'Moved',
        'edit' => 'Edited Address',
    );

    private $_id;

    private $_safeSerializeService;

    private static function makeActionUrl($id)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
        return $baseUrl->baseUrl(App_Resources::MEMBER . '/editClient'
            . (($id !== null) ? '/id/' . urlencode($id) : ''));
    }

    public function __construct($id = null, $readOnly = false)
    {
        $this->_id = $id;
        $this->_safeSerializeService = new App_Service_SafeSerialize();

        parent::__construct(array(
            'action' => self::makeActionUrl($id),
            'method' => 'post',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/client-form.phtml',
                    'editing' => ($id !== null),
                    'readOnly' => $readOnly,
                )),
                'Form',
            ),
            'class' => 'form-horizontal twocol',
        ));

        // Hidden element storing IDs and things across edit requests:

        $safeSerializedNull = $this->_safeSerializeService->serialize(null);

        $this->addElement('hidden', 'fixedClientData', array(
            'value' => $safeSerializedNull['serial'],
            'decorators' => array('ViewHelper'),
        ));

        $this->addElement('hidden', 'fixedClientDataHash', array(
            'value' => $safeSerializedNull['hash'],
            'decorators' => array('ViewHelper'),
        ));

        // General summary elements for clients with existing database entries:

        if ($id !== null) {
            $this->addElement('text', 'clientId', array(
                'label' => 'Client ID',
                'readonly' => true,
                'dimension' => 2,
            ));

            $this->addElement('text', 'userName', array(
                'label' => 'Creating user',
                'readonly' => true,
                'dimension' => 3,
            ));

            $this->addElement('text', 'createdDate', array(
                'label' => 'Creation date',
                'readonly' => true,
                'dimension' => 2,
            ));
        }

        // Personal information elements:

        $this->addElement('text', 'firstName', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a first name.'),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'First name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'First name',
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addElement('text', 'lastName', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a last name.'),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Last name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'Last name',
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addElement('text', 'otherName', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Other name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'Other name',
            'description' => '(Optional)',
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addElement('text', 'birthDate', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('Date', true, array(
                    'format' => 'MM/dd/yyyy',
                    'messages' => array(
                        'dateInvalidDate' => 'Birth date must be properly formatted.',
                        'dateFalseFormat' => 'Birth date must be a valid date.',
                    ),
                )),
            ),
            'label' => 'Birth date',
            'description' => '(Optional)',
            'maxlength' => 10,
            'dimension' => 2,
            'class' => 'date',
        ));

        $this->addElement('text', 'ssn4', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must be four digits.'),
                )),
                array('Digits', true, array(
                    'messages' => array('notDigits' => 'Must be four digits.'),
                )),
                array('StringLength', true, array(
                    'min' => 4,
                    'max' => 4,
                    'messages' => array(
                        'stringLengthTooShort' => 'Must be four digits.',
                        'stringLengthTooLong' => 'Must be four digits.',
                    ),
                )),
            ),
            'label' => 'Last four digits of SSN',
            'description' => '(Optional)',
            'maxlength' => 4,
            'dimension' => 1,
        ));

        $this->addElement('select', 'maritalStatus', array(
            'multiOptions' => $this->_MARRIAGE_OPTIONS,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must choose a marital status.'),
                )),
                array('InArray', true, array(
                    'haystack' => array_keys($this->_MARRIAGE_OPTIONS),
                    'strict' => true,
                    'messages' => array('notInArray' => 'You must choose a marital status.'),
                )),
            ),
            'label' => 'Marital status',
            'dimension' => 2,
        ));

        $this->addElement('text', 'spouseName', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => "You must enter a spouse's first name."),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong'
                            => "Spouse's first name must be shorter than 30 characters.",
                    ),
                )),
            ),
            'label' => "Spouse's first name",
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addElement('text', 'spouseBirthDate', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('Date', true, array(
                    'format' => 'MM/dd/yyyy',
                    'messages' => array(
                        'dateInvalidDate' => "Spouse's birth date must be properly formatted.",
                        'dateFalseFormat' => "Spouse's birth date must be a valid date.",
                    ),
                )),
            ),
            'label' => "Spouse's birth date",
            'description' => '(Optional)',
            'maxlength' => 10,
            'dimension' => 2,
            'class' => 'date',
        ));

        $this->addElement('text', 'spouseSsn4', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must be four digits.'),
                )),
                array('Digits', true, array(
                    'messages' => array('notDigits' => 'Must be four digits.'),
                )),
                array('StringLength', true, array(
                    'min' => 4,
                    'max' => 4,
                    'messages' => array(
                        'stringLengthTooShort' => 'Must be four digits.',
                        'stringLengthTooLong' => 'Must be four digits.',
                    ),
                )),
            ),
            'label' => "Last four digits of spouse's SSN",
            'description' => '(Optional)',
            'maxlength' => 4,
            'dimension' => 1,
        ));

        // Additional information elements:

        $this->addElement('checkbox', 'doNotHelp', array(
            'required' => true,
            'label' => 'Do not help',
        ));

        $this->addElement('text', 'doNotHelpReason', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a "do not help" reason.'),
                )),
                array('StringLength', true, array(
                    'max' => 100,
                    'messages' => array(
                        'stringLengthTooLong'
                            => '"Do not help" reason must be shorter than 100 characters.',
                    ),
                )),
            ),
            'label' => '"Do not help" reason',
            'maxlength' => 100,
            'dimension' => 3,
        ));

        $this->addElement('checkbox', 'veteran', array(
            'required' => true,
            'label' => 'Veteran',
        ));

        $this->addElement('select', 'parish', array(
            'multiOptions' => $this->_PARISH_OPTIONS,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must choose a parish name.'),
                )),
                array('InArray', true, array(
                    'haystack' => array_keys($this->_PARISH_OPTIONS),
                    'strict' => true,
                    'messages' => array('notInArray' => 'You must choose a parish name.'),
                )),
            ),
            'label' => 'Parish attended',
            'dimension' => 3,
        ));

        // Contact information elements:

        $this->addElement('text', 'cellPhone', array(
            'filters' => array('StringTrim', 'Digits'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => ''),
                )),
                array('StringLength', true, array(
                    'min' => 10,
                    'max' => 10,
                    'messages' => array(
                        'stringLengthTooShort' => 'Cell phone must be a ten digit number.',
                        'stringLengthTooLong' => 'Cell phone must be a ten digit number.',
                    ),
                )),
            ),
            'label' => 'Cell phone',
            'maxlength' => 12,
            'dimension' => 2,
            'class' => 'phone',
        ));

        $this->addElement('text', 'homePhone', array(
            'filters' => array('StringTrim', 'Digits'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => ''),
                )),
                array('StringLength', true, array(
                    'min' => 10,
                    'max' => 10,
                    'messages' => array(
                        'stringLengthTooShort' => 'Home phone must be a ten digit number.',
                        'stringLengthTooLong' => 'Home phone must be a ten digit number.',
                    ),
                )),
            ),
            'label' => 'Home phone',
            'maxlength' => 12,
            'dimension' => 2,
            'class' => 'phone',
        ));

        $this->addElement('text', 'workPhone', array(
            'filters' => array('StringTrim', 'Digits'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => ''),
                )),
                array('StringLength', true, array(
                    'min' => 10,
                    'max' => 10,
                    'messages' => array(
                        'stringLengthTooShort' => 'Work phone must be a ten digit number.',
                        'stringLengthTooLong' => 'Work phone must be a ten digit number.',
                    ),
                )),
            ),
            'label' => 'Work phone',
            'maxlength' => 12,
            'dimension' => 2,
            'class' => 'phone',
        ));

        if ($id !== null) {
            $this->addElement('select', 'changeType', array(
                'multiOptions' => $this->_CHANGE_TYPE_OPTIONS,
                'validators' => array(
                    array('InArray', true, array(
                        'haystack' => array_keys($this->_CHANGE_TYPE_OPTIONS),
                        'strict' => true,
                        'messages' => array('notInArray' => 'Must choose a change type.'),
                    )),
                ),
                'label' => 'Change type',
                'dimension' => 2,
            ));
        }

        $this->addSubForm(new Application_Model_Member_AddrSubForm(array(
            'hasParishField' => true,
            'readOnly' => $readOnly,
            'zipRequired' => true,
        )), 'addr');

        // Householders sub form:

        $this->addSubForm(
            new Application_Model_Member_HouseholderRecordListSubForm($id !== null, $readOnly),
            'householderRecordList'
        );

        // Employers sub form:

        $this->addSubForm(
            new Application_Model_Member_EmployerRecordListSubForm($id !== null, $readOnly),
            'employerRecordList'
        );

        // Primary form actions:

        $this->addElement('submit', 'submit', array(
            'label' => ($id === null) ? 'Create Client' : 'Submit Changes',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));

        // If necessary, mark all the form elements read only.

        if ($readOnly) {
            foreach ($this->getElements() as $element) {
                if ($element instanceof Zend_Form_Element_Select) {
                    $element->setAttrib('disabled', true);
                } else {
                    $element->setAttrib('readonly', true);
                }
            }
        }
    }

    public function preValidate($data)
    {
        if (isset($data['maritalStatus']) && $data['maritalStatus'] === 'Married') {
            $this->spouseName->setRequired(true);
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

        $this->householderRecordList->preValidate($data);
        $this->employerRecordList->preValidate($data);
    }

    public function handleAddRemoveRecords($data)
    {
        return $this->householderRecordList->handleAddRemoveRecords($data)
            || $this->employerRecordList->handleAddRemoveRecords($data);
    }

    public function getClient()
    {
        $client = new Application_Model_Impl_Client();
        $client->setId($this->_id)
               ->setFirstName(App_Formatting::emptyToNull($this->firstName->getValue()))
               ->setLastName(App_Formatting::emptyToNull($this->lastName->getValue()))
               ->setOtherName(App_Formatting::emptyToNull($this->otherName->getValue()))
               ->setMaritalStatus(App_Formatting::emptyToNull($this->maritalStatus->getValue()))
               ->setBirthDate(App_Formatting::unformatDate($this->birthDate->getValue()))
               ->setSsn4(App_Formatting::emptyToNull($this->ssn4->getValue()))
               ->setCellPhone(App_Formatting::emptyToNull($this->cellPhone->getValue()))
               ->setHomePhone(App_Formatting::emptyToNull($this->homePhone->getValue()))
               ->setWorkPhone(App_Formatting::emptyToNull($this->workPhone->getValue()))
               ->setParish(App_Formatting::emptyToNull($this->parish->getValue()))
               ->setVeteran($this->veteran->isChecked())
               ->setCurrentAddr($this->addr->getAddr());

        $spouse = new Application_Model_Impl_Client();

        if ($client->isMarried()) {
            $spouse->setFirstName(App_Formatting::emptyToNull($this->spouseName->getValue()))
                   ->setLastName($client->getLastName())
                   ->setMaritalStatus($client->getMaritalStatus())
                   ->setBirthDate(App_Formatting::unformatDate($this->spouseBirthDate->getValue()))
                   ->setSsn4(App_Formatting::emptyToNull($this->spouseSsn4->getValue()))
                   ->setHomePhone($client->getHomePhone())
                   ->setParish($client->getParish());
        }

        $client->setSpouse($spouse);

        $fixedClientData = $this->_safeSerializeService->unserialize(
            $this->fixedClientData->getValue(),
            $this->fixedClientDataHash->getValue()
        );

        if (isset($fixedClientData['userId'], $fixedClientData['householdId'],
                $fixedClientData['createdDate'])) {
            $user = new Application_Model_Impl_User();
            $user->setUserId($fixedClientData['userId']);

            $client
                ->setUser($user)
                ->setHouseholdId($fixedClientData['householdId'])
                ->setCreatedDate($fixedClientData['createdDate']);
        }

        if (isset($fixedClientData['spouseId'])) {
            $client->getSpouse()->setId($fixedClientData['spouseId']);
        }

        if ($this->doNotHelp->isChecked()) {
            $doNotHelp = new Application_Model_Impl_DoNotHelp();
            $doNotHelp->setReason(App_Formatting::emptyToNull($this->doNotHelpReason->getValue()));

            if (isset($fixedClientData['doNotHelpUserId'], $fixedClientData['doNotHelpDate'])) {
                $doNotHelpUser = new Application_Model_Impl_User();
                $doNotHelpUser->setUserId($fixedClientData['doNotHelpUserId']);

                $doNotHelp
                    ->setUser($doNotHelpUser)
                    ->setDateAdded($fixedClientData['doNotHelpDate']);
            }

            $client->setDoNotHelp($doNotHelp);
        }

        return $client;
    }

    public function setClient($client)
    {
        // Save fixed client IDs and other things that shouldn't be editable.
        $fixedClientData = array();

        if ($client->getUser() !== null) {
            $fixedClientData['userId']        = $client->getUser()->getUserId();
            $fixedClientData['householdId']   = $client->getHouseholdId();
            $fixedClientData['maritalStatus'] = $client->getMaritalStatus();
            $fixedClientData['createdDate']   = $client->getCreatedDate();
        }

        if ($client->isMarried()) {
            $fixedClientData['spouseId'] = $client->getSpouse()->getId();
        }

        if ($client->isDoNotHelp()) {
            $doNotHelp = $client->getDoNotHelp();

            $fixedClientData['doNotHelpUserId'] = $doNotHelp->getUser()->getUserId();
            $fixedClientData['doNotHelpDate']   = $doNotHelp->getDateAdded();

            $doNotHelpReason = $doNotHelp->getReason();
        } else {
            $doNotHelpReason = '';
        }

        $safeSerializedFixedClientData = $this->_safeSerializeService->serialize($fixedClientData);

        $this->fixedClientData->setValue($safeSerializedFixedClientData['serial']);
        $this->fixedClientDataHash->setValue($safeSerializedFixedClientData['hash']);

        // Populate user-visible client fields.
        if ($this->_id !== null) {
            $this->clientId->setValue($this->_id);
            $this->userName->setValue($client->getUser()->getFullName());
            $this->createdDate->setValue(App_Formatting::formatDate($client->getCreatedDate()));
        }

        $this->firstName->setValue($client->getFirstName());
        $this->lastName->setValue($client->getLastName());
        $this->otherName->setValue($client->getOtherName());
        $this->maritalStatus->setValue($client->getMaritalStatus());
        $this->birthDate->setValue(App_Formatting::formatDate($client->getBirthDate()));
        $this->ssn4->setValue($client->getSsn4());
        $this->doNotHelp->setChecked($client->isDoNotHelp());
        $this->doNotHelpReason->setValue($doNotHelpReason);
        $this->veteran->setChecked($client->isVeteran());
        $this->parish->setValue($client->getParish());
        $this->homePhone->setValue($client->getFormattedHomePhone());
        $this->cellPhone->setValue($client->getFormattedCellPhone());
        $this->workPhone->setValue($client->getFormattedWorkPhone());

        if ($client->getCurrentAddr() !== null) {
            $this->addr->setAddr($client->getCurrentAddr());
        }

        if ($client->isMarried()) {
            $spouse = $client->getSpouse();
            $this->spouseName->setValue($spouse->getFirstName());
            $this->spouseBirthDate->setValue(App_Formatting::formatDate($spouse->getBirthDate()));
            $this->spouseSsn4->setValue($spouse->getSsn4());
        }
    }

    public function getRemovedHouseholders()
    {
        return $this->householderRecordList->getRemovedRecords();
    }

    public function getChangedHouseholders()
    {
        return $this->householderRecordList->getChangedRecords();
    }

    public function setHouseholders($householders)
    {
        $this->householderRecordList->setRecords($householders);
    }

    public function getRemovedEmployers()
    {
        return $this->employerRecordList->getRemovedRecords();
    }

    public function getChangedEmployers()
    {
        return $this->employerRecordList->getChangedRecords();
    }

    public function setEmployers($employers)
    {
        $this->employerRecordList->setRecords($employers);
    }

    public function isMove()
    {
        return $this->_id !== null && $this->changeType->getValue() === 'move';
    }

    public function isMaritalStatusChange()
    {
        $fixedClientData = $this->_safeSerializeService->unserialize(
            $this->fixedClientData->getValue(),
            $this->fixedClientDataHash->getValue()
        );

        return $this->_id !== null
            && $fixedClientData
            && $fixedClientData['maritalStatus'] !== $this->maritalStatus->getValue();
    }
}
