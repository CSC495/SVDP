<?php

class Application_Model_Member_ReferralForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_REFERRED_TO_OPTIONS = array(
        '' => '',
        'County Social Services' => 'County Social Services',
        'LIHEAP' => 'LIHEAP',
        'Loaves & Fishes' => 'Loaves & Fishes',
        'Naperville CARES' => 'Naperville CARES',
        'PADS' => 'PADS',
        'Sharing Connection' => 'Sharing Connection',
        'SVDP Thrift Stores' => 'SVDP Thrift Stores',
        'Samaritan Interfaith' => 'Samaritan Interfaith',
        'Township' => 'Township',
        'Other' => 'Other',
    );

    public function __construct($case, $needId)
    {
        $needs = $case->getNeeds();
        $need  = $needs[$needId];

        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'action' => $baseUrl->baseUrl(
                App_Resources::MEMBER
                . '/newReferral/caseId/'
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

        $this->addElement('text', 'reason', array(
            'label' => 'Reason for referral',
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a referral reason.'),
                )),
            ),
            'dimension' => 5,
        ));

        $this->addElement('select', 'referredTo', array(
            'label' => 'Referral destination',
            'multiOptions' => $this->_REFERRED_TO_OPTIONS,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must choose a referral destination.'),
                )),
                array('InArray', true, array(
                    'haystack' => array_keys($this->_REFERRED_TO_OPTIONS),
                    'strict' => true,
                    'messages' => array('notInArray' => 'You must choose a referral destination.'),
                )),
            ),
            'class' => 'span3',
        ));

        $this->addDisplayGroup(
            array('need', 'amount', 'reason', 'referredTo'),
            'referral',
            array('legend' => 'Referral information:')
        );

        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'label' => 'Create Referral',
        ));

        $this->addDisplayGroup(
            array('submit'),
            'actions',
            array('disableLoadDefaultDecorators' => true, 'decorators' => array('Actions'))
        );
    }

    public function getReferral()
    {
        $referral = new Application_Model_Impl_Referral();
        $referral
            ->setReason(App_Formatting::emptyToNull($this->reason->getValue()))
            ->setReferredTo(App_Formatting::emptyToNull($this->referredTo->getValue()));

        return $referral;
    }
}
