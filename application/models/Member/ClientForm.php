<?php

class Application_Model_Member_ClientForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function __construct($id = null)
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl(App_Resources::MEMBER) . '/editclient')
             ->setMethod('post');

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
            'maxLength' => 30,
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
            'maxLength' => 30,
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
            'description' => '(optional)',
            'maxLength' => 30,
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
            'label' => 'Birth date (mm/dd/yyyy)',
            'description' => '(optional)',
            'maxLength' => 10,
            'dimension' => 2,
        ));

        $this->addElement('checkbox', 'married', array(
            'required' => true,
            'label' => 'Currently married',
        ));

        $this->addElement('text', 'spouseName', array(
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
            'label' => "Spouse's name",
            'maxLength' => 30,
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
            'label' => "Spouse's birth date (mm/dd/yyyy)",
            'description' => '(optional)',
            'maxLength' => 10,
            'dimension' => 2,
        ));

        $this->addElement('text', 'ssn4', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(
                    'min' => 4,
                    'max' => 4,
                    'messages' => array(
                        'stringLengthTooShort' => 'SSN must be last four digits.',
                        'stringLengthTooLong' => 'SSN must be last four digits.',
                    ),
                )),
            ),
            'label' => 'Last four digits of SSN',
            'maxLength' => 4,
            'dimension' => 1,
        ));

        $this->addDisplayGroup(
            array(
                'firstName',
                'lastName',
                'otherName',
                'birthDate',
                'married',
                'spouseName',
                'spouseBirthDate',
                'ssn4',
            ),
            'personal',
            array('legend' => 'Personal information:')
        );

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
            'maxLength' => 100,
            'dimension' => 4,
        ));

        $this->addElement('checkbox', 'veteran', array(
            'required' => true,
            'label' => 'Veteran',
        ));

        $this->addElement('text', 'parish', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a parish name.'),
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => array(
                        'stringLengthTooLong' => 'Parish name must be shorter than 50 characters.',
                    ),
                )),
            ),
            'label' => 'Parish attended',
            'maxLength' => 50,
            'dimension' => 3,
        ));

        $this->addDisplayGroup(
            array('doNotHelp', 'doNotHelpReason', 'veteran', 'parish'),
            'extra',
            array('legend' => 'Additional information:')
        );

        $this->addElement('text', 'cellPhone', array(
            'filters' => array('StringTrim', 'Digits'),
            'validators' => array(
                array('StringLength', true, array(
                    'min' => 10,
                    'max' => 10,
                    'messages' => array(
                        'stringLengthTooShort' => 'Cell phone must be a ten digit phone number.',
                        'stringLengthTooLong' => 'Cell phone must be a ten digit phone number.',
                    ),
                )),
            ),
            'label' => 'Cell phone',
            'description' => '(at least one phone required)',
            'maxLength' => 12,
            'dimension' => 2,
        ));

        $this->addElement('text', 'homePhone', array(
            'filters' => array('StringTrim', 'Digits'),
            'validators' => array(
                array('StringLength', true, array(
                    'min' => 10,
                    'max' => 10,
                    'messages' => array(
                        'stringLengthTooShort' => 'Home phone must be a ten digit phone number.',
                        'stringLengthTooLong' => 'Home phone must be a ten digit phone number.',
                    ),
                )),
            ),
            'label' => 'Home phone',
            'description' => '(at least one phone required)',
            'maxLength' => 12,
            'dimension' => 2,
        ));

        $this->addElement('text', 'workPhone', array(
            'filters' => array('StringTrim', 'Digits'),
            'validators' => array(
                array('StringLength', true, array(
                    'min' => 10,
                    'max' => 10,
                    'messages' => array(
                        'stringLengthTooShort' => 'Work phone must be a ten digit phone number.',
                        'stringLengthTooLong' => 'Work phone must be a ten digit phone number.',
                    ),
                )),
            ),
            'label' => 'Work phone',
            'description' => '(at least one phone required)',
            'maxLength' => 12,
            'dimension' => 2,
        ));

        $this->addDisplayGroup(
            array('cellPhone', 'homePhone', 'workPhone'),
            'contact',
            array('legend' => 'Contact information:')
        );

        $this->addSubForm(
            new Application_Model_Member_AddressSubForm('Current address:', true, true),
            'addr'
        );

        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'label' => ($id === null) ? 'Create Client' : 'Submit Changes'
        ));

        $this->addDisplayGroup(
            array('submit'),
            'actions',
            array('disableLoadDefaultDecorators' => true, 'decorators' => array('Actions'))
        );
    }

    public function setClient($client)
    {
        $this->firstName->setValue($client->getFirstName());
        $this->lastName->setValue($client->getLastName());
        $this->otherName->setValue($client->getOtherName());
        $this->married->setChecked($client->isMarried());
        $this->birthDate->setValue($client->getBirthDate());
        $this->ssn4->setValue($client->getSsn4());
        $this->doNotHelp->setChecked($client->isDoNotHelp());
        $this->veteran->setChecked($client->isVeteran());
        $this->parish->setValue($client->getParish());
        $this->homePhone->setValue($client->getFormattedHomePhone());
        $this->cellPhone->setValue($client->getFormattedCellPhone());
        $this->workPhone->setValue($client->getFormattedWorkPhone());
        $this->addr->setAddr($client->getCurrentAddr());
    }

    public function getClient()
    {
        $client = new Application_Model_Impl_Client();
        $client->setId($this->_id)
               ->setFirstName($this->firstName->getValue())
               ->setLastName($this->lastName->getValue())
               ->setOtherName(self::emptyToNull($this->otherName->getValue()))
               ->setCurrentAddr($this->addr->getAddr());

        return $client;
    }

    private static function emptyToNull($x)
    {
        return ($x !== '') ? $x : null;
    }
}
