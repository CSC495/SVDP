<?php

class Application_Model_Member_HouseholderRecordListSubForm
    extends App_Form_RecordListSubFormAbstract {

    public function __construct($showDirtyMsg, $readOnly)
    {
        parent::__construct(array(
            'namespace' => 'householder',
            'labels' => array(
                'First Name',
                'Last Name',
                'Relationship',
                'Birth Date (Optional)',
                'Departure Date (Optional)',
            ),
            'readOnly' => $readOnly,
            'legend' => 'Household members:',
            'addRecordMsg' => 'Add Another Household Member',
            'noRecordsMsg' => 'No household members listed.',
            'dirtyMsg' => $showDirtyMsg ? 'Click "Submit Changes" to save household members.' : '',
        ));
    }

    protected function initSubForm($householderSubForm)
    {
        $householderSubForm->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

        $householderSubForm->addElement('text', 'firstName', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must not be empty.'),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Must not be more than 30 characters.',
                    ),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'Addon',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
            ),
            'maxlength' => 30,
            'class' => 'span2',
        ));

        $householderSubForm->addElement('text', 'lastName', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must not be empty.'),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Must not be more than 30 characters.',
                    ),
                )),
            ),
            'maxlength' => 30,
            'class' => 'span2',
        ));

        $householderSubForm->addElement('text', 'relationship', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must not be empty.'),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Must not be more than 30 characters.',
                    ),
                )),
            ),
            'maxlength' => 30,
            'class' => 'span2',
        ));

        $householderSubForm->addElement('text', 'birthDate', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('Date', true, array(
                    'format' => 'MM/dd/yyyy',
                    'messages' => array(
                        'dateInvalidDate' => 'Must be properly formatted.',
                        'dateFalseFormat' => 'Must be a valid date.',
                    ),
                )),
            ),
            'maxlength' => 10,
            'class' => 'span2 date',
        ));

        $householderSubForm->addElement('text', 'departDate', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('Date', true, array(
                    'format' => 'MM/dd/yyyy',
                    'messages' => array(
                        'dateInvalidDate' => 'Must be properly formatted.',
                        'dateFalseFormat' => 'Must be a valid date.',
                    ),
                )),
            ),
            'maxlength' => 10,
            'class' => 'span2 date',
        ));
    }

    protected function getRecord($householderSubForm)
    {
        $householder = new Application_Model_Impl_Householder();
        $householder->setId(App_Formatting::emptyToNull($householderSubForm->id->getValue()));
        $householder->setFirstName(
            App_Formatting::emptyToNull($householderSubForm->firstName->getValue()));
        $householder->setLastName(
            App_Formatting::emptyToNull($householderSubForm->lastName->getValue()));
        $householder->setRelationship(
            App_Formatting::emptyToNull($householderSubForm->relationship->getValue()));
        $householder->setBirthDate(
            App_Formatting::unformatDate($householderSubForm->birthDate->getValue()));
        $householder->setDepartDate(
            App_Formatting::unformatDate($householderSubForm->departDate->getValue()));

        return $householder;
    }

    protected function setRecord($householderSubForm, $householder)
    {
        $householderSubForm->id->setValue($householder->getId());
        $householderSubForm->firstName->setValue($householder->getFirstName());
        $householderSubForm->lastName->setValue($householder->getLastName());
        $householderSubForm->relationship->setValue($householder->getRelationship());
        $householderSubForm->birthDate->setValue(
            App_Formatting::formatDate($householder->getBirthDate()));
        $householderSubForm->departDate->setValue(
            App_Formatting::formatDate($householder->getDepartDate()));
    }
}
