<?php

class Application_Model_Member_CheckReqForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function __construct(Application_Model_Impl_Case $case, $needId)
    {
        $needs = $case->getNeeds();
        $need  = $needs[$needId];

        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'action' => $baseUrl->baseUrl(
                App_Resources::MEMBER
                . '/newCheckReq/caseId/'
                . urlencode($case->getId())
                . '/needId/'
                . urlencode($needId)
            ),
            'method' => 'post',
        ));

        $this->addElement('text', 'need', array(
            'label' => 'Need',
            'value' => $need->getNeed(),
            'dimension' => 2,
            'readonly' => true,
        ));

        $this->addElement('text', 'amount', array(
            'label' => 'Amount',
            'value' => App_Formatting::formatCurrency($need->getAmount()),
            'dimension' => 2,
            'prepend' => '$',
            'readonly' => true,
        ));

        $this->addElement('textarea', 'comment', array(
            'filters' => array('StringTrim'),
            'label' => 'Comment',
            'description' => '(Optional)',
            'dimension' => 7,
            'rows' => 4,
        ));

        $this->addDisplayGroup(
            array('need', 'amount', 'comment'),
            'generalInfo',
            array('legend' => 'General information:')
        );

        $this->addElement('text', 'accountNumber', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Account number must be provided.'),
                )),
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong'
                            => 'Account number must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'Account number',
            'description' => '(Enter "None" if unknown or not applicable)',
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addElement('text', 'payeeName', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a payee name.'),
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => array(
                        'stringLengthTooLong' => 'First name must be shorter than 50 characters.',
                    ),
                )),
            ),
            'label' => 'Payee name',
            'maxlength' => 50,
            'dimension' => 5,
        ));

        $this->addElement('text', 'phone', array(
            'required' => true,
            'filters' => array('StringTrim', 'Digits'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Payee phone must be provided.'),
                )),
                array('StringLength', true, array(
                    'min' => 10,
                    'max' => 10,
                    'messages' => array(
                        'stringLengthTooShort' => 'Payee phone must be a ten digit number.',
                        'stringLengthTooLong' => 'Payee phone must be a ten digit number.',
                    ),
                )),
            ),
            'label' => 'Payee phone',
            'maxlength' => 12,
            'dimension' => 2,
            'class' => 'phone',
        ));

        $this->addElement('text', 'contactFirstName', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'First name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'Contact first name',
            'description' => '(Optional)',
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addElement('text', 'contactLastName', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Last name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'Contast last name',
            'description' => '(Optional)',
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addDisplayGroup(
            array('accountNumber', 'payeeName', 'phone', 'contactFirstName', 'contactLastName'),
            'general',
            array('legend' => 'Payee contact information:')
        );

        $this->addSubForm(new Application_Model_Member_AddrSubForm(array(
            'title' => 'Payee address:',
            'hideAptField' => true,
            'zipRequired' => true,
        )), 'addr' );

        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'label' => 'Create Check Request',
        ));

        $this->addDisplayGroup(
            array('submit'),
            'actions',
            array('disableLoadDefaultDecorators' => true, 'decorators' => array('Actions'))
        );
    }

    public function getCheckReq()
    {
        $checkReq = new Application_Model_Impl_CheckReq();
        $checkReq
            ->setComment(App_Formatting::emptyToNull($this->comment->getValue()))
            ->setAccountNumber(App_Formatting::emptyToNull($this->accountNumber->getValue()))
            ->setPayeeName(App_Formatting::emptyToNull($this->payeeName->getValue()))
            ->setAddress($this->addr->getAddr())
            ->setContactFirstName(App_Formatting::emptyToNull($this->contactFirstName->getValue()))
            ->setPhone($this->phone->getValue())
            ->setContactLastName(App_Formatting::emptyToNull($this->contactLastName->getValue()));

        return $checkReq;
    }
}
