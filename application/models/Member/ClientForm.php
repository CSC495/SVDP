<?php

class Application_Model_Member_ClientForm extends Zend_Form
{

    private $_id;

    private $_householderSubForms = array();

    private $_employerSubForms = array();

    private static function makeActionUrl($id)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
        return $baseUrl->baseUrl(App_Resources::MEMBER . '/editClient'
            . (($id !== null) ? '/id/' . urlencode($id) : ''));
    }

    public function __construct($id = null)
    {
        $this->_id = $id;

        parent::__construct();

        $this
            ->setAction(self::makeActionUrl($id))
            ->setMethod('post')
            ->setDecorators(array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/editclient.phtml',
                    'householderSubForms' => &$this->_householderSubForms,
                    'employerSubForms' => &$this->_employerSubForms,
                )),
                array('Form', array('class' => 'member form-horizontal')),
            ))
            ->setElementDecorators(array(
                'ViewHelper',
                array('Description', array('class' => 'help-block')),
                array('HtmlTag', array('tag' => 'div', 'class' => 'controls')),
                array('Label', array('class' => 'control-label')),
            ));

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
            'class' => 'span3',
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
            'class' => 'span3',
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
            'maxlength' => 30,
            'class' => 'span3',
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
            'description' => '(optional)',
            'maxlength' => 10,
            'class' => 'span2',
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
            'maxlength' => 4,
            'class' => 'span1',
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
            'maxlength' => 30,
            'class' => 'span3',
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
            'description' => '(optional)',
            'maxlength' => 10,
            'class' => 'span2',
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
            'class' => 'span3',
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
            'maxlength' => 50,
            'class' => 'span3',
        ));

        // Contact information elements:

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
            'maxlength' => 12,
            'class' => 'span2',
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
            'maxlength' => 12,
            'class' => 'span2',
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
            'maxlength' => 12,
            'class' => 'span2',
        ));

        $this->addSubForm(new Application_Model_Member_AddressSubForm(null, true, true), 'addr');

        // Householder elements:

        $this->addElement('submit', 'newHouseholder', array(
            'label' => 'Add Another Member',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-info',
        ));

        // Employer elements:

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

    /*public function getClient()
    {
        $client = new Application_Model_Impl_Client();
        $client->setId($this->_id)
               ->setFirstName($this->firstName->getValue())
               ->setLastName($this->lastName->getValue())
               ->setOtherName(self::emptyToNull($this->otherName->getValue()))
               ->setCurrentAddr($this->addr->getAddr());

        return $client;
    }*/

    public function setClient($client)
    {
        $this->firstName->setValue($client->getFirstName());
        $this->lastName->setValue($client->getLastName());
        $this->otherName->setValue($client->getOtherName());
        $this->married->setChecked($client->isMarried());
        $this->birthDate->setValue($client->getBirthDate());
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
            $this->spouseBirthDate->setValue($spouse->getBirthDate());
        }
    }

    public function setHouseholders($householders)
    {
        $i = 0;

        foreach ($householders as $householder) {
            $householderSubForm = new Application_Model_Member_HouseholderSubForm();
            $householderSubForm->setHouseholder($householder);

            $this->_householderSubForms[] = $householderSubForm;
            $this->addSubForm($householderSubForm, "householder$i");

            ++$i;
        }
    }

    private static function emptyToNull($x)
    {
        return ($x !== '') ? $x : null;
    }
}
