<?php

class Application_Model_Member_EmployerRecordListSubForm
    extends App_Form_RecordListSubFormAbstract {

    public function __construct($showDirtyMsg, $readOnly)
    {
        parent::__construct(array(
            'namespace' => 'employer',
            'labels' => array(
                'Company',
                'Position',
                'Start Date',
                'End Date (Optional)',
            ),
            'readOnly' => $readOnly,
            'legend' => 'Employment status:',
            'addRecordMsg' => 'Add Another Employer',
            'noRecordsMsg' => 'No employers listed.',
            'dirtyMsg' => $showDirtyMsg ? 'Click "Submit Changes" to save employers.' : '',
        ));
    }

    protected function initSubForm($employerSubForm)
    {
        $employerSubForm->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

        $employerSubForm->addElement('text', 'company', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must not be empty.'),
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => array(
                        'stringLengthTooLong' => 'Must not be more than 50 characters.',
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
            'maxlength' => 50,
            'class' => 'span2',
        ));

        $employerSubForm->addElement('text', 'position', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must not be empty.'),
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => array(
                        'stringLengthTooLong' => 'Must not be more than 50 characters.',
                    ),
                )),
            ),
            'maxlength' => 50,
            'class' => 'span2',
        ));

        $employerSubForm->addElement('text', 'startDate', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must not be empty.'),
                )),
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

        $employerSubForm->addElement('text', 'endDate', array(
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

    protected function getRecord($employerSubForm)
    {
        $employer = new Application_Model_Impl_Employer();
        $employer->setId(App_Formatting::emptyToNull($employerSubForm->id->getValue()));
        $employer->setCompany(App_Formatting::emptyToNull($employerSubForm->company->getValue()));
        $employer->setPosition(App_Formatting::emptyToNull($employerSubForm->position->getValue()));
        $employer->setStartDate(
            App_Formatting::unformatDate($employerSubForm->startDate->getValue()));
        $employer->setEndDate(App_Formatting::unformatDate($employerSubForm->endDate->getValue()));

        return $employer;
    }

    protected function setRecord($employerSubForm, $employer)
    {
        $employerSubForm->id->setValue($employer->getId());
        $employerSubForm->company->setValue($employer->getCompany());
        $employerSubForm->position->setValue($employer->getPosition());
        $employerSubForm->startDate->setValue(
            App_Formatting::formatDate($employer->getStartDate()));
        $employerSubForm->endDate->setValue(App_Formatting::formatDate($employer->getEndDate()));
    }
}
