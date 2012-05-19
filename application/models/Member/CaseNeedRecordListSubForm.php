<?php

class Application_Model_Member_CaseNeedRecordListSubForm
    extends App_Form_RecordListSubFormAbstract
{

    private $_NEED_OPTIONS = array(
        '' => '',
        'Auto' => 'Auto',
        'Basic Needs Form' => 'Basic Needs Form',
        'Clothing' => 'Clothing',
        'Electricity' => 'Electricity',
        'Food' => 'Food',
        'Furniture' => 'Furniture',
        'Gas Heat' => 'Gas Heat',
        'Gasoline Card' => 'Gasoline Card',
        'Home Repars' => 'Home Repairs',
        'Legal Assistance' => 'Legal Assistance',
        'Lodging' => 'Lodging',
        'Mattress' => 'Mattress',
        'Medical Bills' => 'Medical Bills',
        'Misc' => 'Misc',
        'Mortgage' => 'Mortgage',
        'Moving Expenses' => 'Moving Expenses',
        'Other' => 'Other',
        'Phone' => 'Phone',
        'Referral' => 'Referral',
        'Rent' => 'Rent',
        'Security Deposit' => 'Security Deposit',
        'Transportation' => 'Transportation',
        'Water' => 'Water',
    );

    public function __construct()
    {
        parent::__construct(array(
            'namespace' => 'caseneed',
            'labels' => array(
                'Need',
                'Amount',
            ),
            'narrow' => true,
            'legend' => 'Case needs:',
            'addRecordMsg' => 'Add Another Need',
            'noRecordsMsg' => 'No needs listed.',
        ));
    }

    protected function initSubForm($caseNeedSubForm)
    {
        $caseNeedSubForm->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

        $caseNeedSubForm->addElement('select', 'need', array(
            'multiOptions' => $this->_NEED_OPTIONS,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must choose a need.'),
                )),
                array('InArray', true, array(
                    'haystack' => array_keys($this->_NEED_OPTIONS),
                    'strict' => true,
                    'messages' => array('notInArray' => 'Must choose a need.'),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'Addon',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
            ),
            'class' => 'span3',
        ));

        $caseNeedSubForm->addElement('text', 'amount', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must enter amount.'),
                )),
                array('Float', true, array(
                    'messages' => array('notFloat' => 'Must be a number.'),
                )),
                array('GreaterThan', true, array(
                    'min' => 0,
                    'messages' => array('notGreaterThan' => 'Must not be negative.'),
                )),
            ),
            'maxlength' => 10,
            'class' => 'span2',
            'prepend' => '$',
        ));
    }

    protected function getRecord($caseNeedSubForm)
    {
        $caseNeed = new Application_Model_Impl_CaseNeed();
        $caseNeed
            ->setId(App_Formatting::emptyToNull($caseNeedSubForm->id->getValue()))
            ->setNeed(App_Formatting::emptyToNull($caseNeedSubForm->need->getValue()))
            ->setAmount(App_Formatting::emptyToNull($caseNeedSubForm->amount->getValue()));

        return $caseNeed;
    }

    protected function setRecord($caseNeedSubForm, $caseNeed)
    {
        $caseNeedSubForm->id->setValue($caseNeed->getId());
        $caseNeedSubForm->need->setValue($caseNeed->getNeed());
        $caseNeedSubForm->amount->setValue($caseNeed->getAmount());
    }
}
