<?php

class Application_Model_Member_CaseNeedRecordListSubForm
    extends App_Form_RecordListSubFormAbstract {

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

            $caseNeedSubForm->addElement('text', 'amount', array(
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
                'prepend' => '$',
            ));
        }

        protected function getRecord($caseNeedSubForm)
        {
            $caseNeed = new Application_Model_Impl_CaseNeed();
            $caseNeed
                ->setCase(App_Formatting::emptyToNull($caseNeedSubForm->id->getValue()))
                ->setNeed(App_Formatting::emptyToNull($caseNeedSubForm->need->getValue()))
                ->setAmount(App_Formatting::emptyToNull($caseNeedSubForm->amount->getValue()));
        }

        protected function setRecord($caseNeedSubForm, $caseNeed)
        {
            $caseNeedSubForm->id->setValue($caseNeed->getId());
            $caseNeedSubForm->need->setValue($caseNeed->getNeed());
            $caseNeedSubForm->amount->setValue($caseNeed->getAmount());
        }
}
