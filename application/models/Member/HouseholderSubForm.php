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

        return $householder;
    }

    public function setHouseholder($householder)
    {
        $this->firstName->setValue($householder->getFirstName());
        $this->lastName->setValue($householder->getLastName());
        $this->relationship->setValue($householder->getRelationship());
        $this->birthDate->setValue($householder->getBirthDate());
        $this->departDate->setValue($householder->getDepartDate());
    }
}
