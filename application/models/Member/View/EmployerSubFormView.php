<?php

class Application_Model_Member_EmployerSubForm extends Zend_Form_SubForm {

    public function __construct()
    {
        parent::__construct();

        $this->addElementPrefixPath(
            'Twitter_Bootstrap_Form_Decorator',
            'Twitter/Bootstrap/Form/Decorator',
            'decorator'
        );

        $this->setDecorators(array(
            'FormElements',
            array('HtmlTag', array('tag' => 'tr')),
        ));

        $this->setElementDecorators(array(
            'ViewHelper',
            'ElementErrors',
            'Wrapper',
            array('HtmlTag', array('tag'  => 'td')),
        ));

        $this->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

        $this->addElement('text', 'company', array(
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
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
            ),
            'maxlength' => 50,
            'class' => 'span2',
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'position', array(
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
        	'attribs'    => array('disabled' => 'disabled'),
        ));

        $this->addElement('text', 'startDate', array(
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
            'class' => 'span2',
        	'attribs'    => array('disabled' => 'disabled'),        		
        ));

        $this->addElement('text', 'endDate', array(
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
            'class' => 'span2',
        	'attribs'    => array('disabled' => 'disabled'),
        ));
    }

    public function getEmployer()
    {
        $employer = new Application_Model_Impl_Employer();
        $employer->setId(App_Formatting::emptyToNull($this->id->getValue()));
        $employer->setCompany(App_Formatting::emptyToNull($this->company->getValue()));
        $employer->setPosition(App_Formatting::emptyToNull($this->position->getValue()));
        $employer->setStartDate(App_Formatting::unformatDate($this->startDate->getValue()));
        $employer->setEndDate(App_Formatting::unformatDate($this->endDate->getValue()));

        return $employer;
    }

    public function setEmployer($employer)
    {
        $this->id->setValue($employer->getId());
        $this->company->setValue($employer->getCompany());
        $this->position->setValue($employer->getPosition());
        $this->startDate->setValue(App_Formatting::formatDate($employer->getStartDate()));
        $this->endDate->setValue(App_Formatting::formatDate($employer->getEndDate()));
    }
}