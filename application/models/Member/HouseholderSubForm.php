<?php

class Application_Model_Member_HouseholderSubForm extends Zend_Form_SubForm {

    public function __construct()
    {
        parent::__construct();

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'tr')),
        ));

        $this->setElementDecorators(array(
            'ViewHelper',
            array('HtmlTag', array('tag'  => 'td')),
        ));

        $this->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

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
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
            ),
            'maxlength' => 30,
            'class' => 'span2',
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
            'maxlength' => 30,
            'class' => 'span2',
        ));

        $this->addElement('text', 'relationship', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a relationship.'),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Last name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'maxlength' => 30,
            'class' => 'span2',
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
            'maxlength' => 10,
            'class' => 'span2',
        ));

        $this->addElement('text', 'departDate', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('Date', true, array(
                    'format' => 'MM/dd/yyyy',
                    'messages' => array(
                        'dateInvalidDate' => 'Departure date must be properly formatted.',
                        'dateFalseFormat' => 'Departure date must be a valid date.',
                    ),
                )),
            ),
            'maxlength' => 10,
            'class' => 'span2',
        ));
    }

    public function getHouseholder()
    {
        $householder = new Application_Model_Impl_Householder();
        $householder->setId(App_Formatting::emptyToNull($this->id->getValue()));
        $householder->setFirstName(App_Formatting::emptyToNull($this->firstName->getValue()));
        $householder->setLastName(App_Formatting::emptyToNull($this->lastName->getValue()));
        $householder->setRelationship(App_Formatting::emptyToNull($this->relationship->getValue()));
        $householder->setBirthDate(App_Formatting::unformatDate($this->birthDate->getValue()));
        $householder->setDepartDate(App_Formatting::unformatDate($this->departDate->getValue()));

        return $householder;
    }

    public function setHouseholder($householder)
    {
        $this->id->setValue($householder->getId());
        $this->firstName->setValue($householder->getFirstName());
        $this->lastName->setValue($householder->getLastName());
        $this->relationship->setValue($householder->getRelationship());
        $this->birthDate->setValue(App_Formatting::formatDate($householder->getBirthDate()));
        $this->departDate->setValue(App_Formatting::formatDate($householder->getDepartDate()));
    }
}
