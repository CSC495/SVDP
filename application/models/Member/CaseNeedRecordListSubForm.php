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
        'Phone' => 'Phone',
        'Referral' => 'Referral',
        'Rent' => 'Rent',
        'Security Deposit' => 'Security Deposit',
        'Transportation' => 'Transportation',
        'Water' => 'Water',
        'Other' => 'Other',
    );

    private $_readOnly;

    private $_caseId;

    private $_showStatus;

    public function __construct($readOnly = false, $caseId = null)
    {
        $this->_readOnly   = $readOnly;
        $this->_caseId     = $caseId;
        $this->_showStatus = ($caseId !== null);

        $labels = $this->_showStatus
                ? array('Status', 'Need', 'Amount', '')
                : array('Need', 'Amount');

        parent::__construct(array(
            'namespace' => 'caseneed',
            'labels' => $labels,
            'readOnly' => $readOnly,
            'narrow' => !$this->_showStatus,
            'legend' => 'Case needs:',
            'addRecordMsg' => 'Add Another Need',
            'noRecordsMsg' => 'No needs listed.',
            'submitMsg' => 'Submit Changes',
        ));
    }

    public function setDefaults(array $defaults)
    {
        parent::setDefaults($defaults);

        foreach ($this->caseneedRecords->getSubForms() as $caseNeedSubForm) {
            $caseNeedSubForm->statusNote->setValue($caseNeedSubForm->status->getValue());
            $caseNeedSubForm->status2Note->setValue($caseNeedSubForm->status2->getValue());
        }

        return $this;
    }

    protected function initSubForm($caseNeedSubForm)
    {
        $caseNeedSubForm->addPrefixPath('App_Form', 'App/Form/');

        if ($this->_showStatus) {
            $caseNeedSubForm->addElement('hidden', 'status', array(
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
                ),
            ));

            $caseNeedSubForm->addElement('note', 'statusNote', array(
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
                ),
            ));
        }

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

        if ($this->_showStatus) {
            $caseNeedSubForm->addElement('hidden', 'status2', array(
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
                ),
            ));

            $caseNeedSubForm->addElement('note', 'status2Note', array(
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
                ),
            ));
        }
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

        if ($this->_showStatus) {
            $this->updateStatus($caseNeedSubForm, $caseNeed);
        }
    }

    private function updateStatus($caseNeedSubForm, $caseNeed)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        $referralOrCheckReq = $caseNeed->getReferralOrCheckReq();

        if ($referralOrCheckReq instanceof Application_Model_Impl_Referral) {
            $this->setSubFormReadOnly($caseNeedSubForm, true);

            $referral = $referralOrCheckReq;

            $status  = '<span class="label label-info">Referred</span>';
            $status2 = 'Referral: '
                     . htmlspecialchars($referral->getReferredTo())
                     . ' ('
                     . htmlspecialchars(App_Formatting::formatDate($referral->getDate()))
                     . ')';
        } else if ($referralOrCheckReq instanceof Application_Model_Impl_CheckReq) {
            $this->setSubFormReadOnly($caseNeedSubForm, true);

            $checkReq        = $referralOrCheckReq;
            $viewCheckReqUrl = $baseUrl->baseUrl(App_Resources::TREASURER . '/checkReq/id/'
                . urlencode($checkReq->getId()));

            $status2 = '<a href="' . htmlspecialchars($viewCheckReqUrl) . '">';

            if ($checkReq->getIssueDate() !== null) {
                $status   = '<span class="label label-success">Issued check</span>';
                $status2 .= 'Issued: '
                          . htmlspecialchars(App_Formatting::formatDate($checkReq->getIssueDate()));
            } else {
                $status   = '<span class="label label-warning">Pending check</span>';
                $status2 .= 'Requested: '
                          . htmlspecialchars(App_Formatting::formatDate(
                                             $checkReq->getRequestDate()));
            }

            $status2 .= '</a>';
        } else {
            $newReferralUrl = $baseUrl->baseUrl(
                App_Resources::MEMBER
                . '/newReferral/caseId/'
                . urlencode($this->_caseId)
                . '/needId/'
                . urlencode($caseNeed->getId())
            );
            $newCheckReqUrl = $baseUrl->baseUrl(
                App_Resources::MEMBER
                . '/newCheckReq/caseId/'
                . urlencode($this->_caseId)
                . '/needId/'
                . urlencode($caseNeed->getId())
                . '/amount/'
                . urlencode($caseNeed->getAmount())
            );

            $status  = '<span class="label label-important">Unprocessed</span>';
            $status2 = '<a href="'
                     . htmlspecialchars($newReferralUrl)
                     . '" class="btn btn-info">Referral</a>'
                     . ' <a href="'
                     . htmlspecialchars($newCheckReqUrl)
                     . '" class="btn btn-info">Check Req.</a>';
        }

        $caseNeedSubForm->status->setValue($status);
        $caseNeedSubForm->status2->setValue($status2);

        $caseNeedSubForm->statusNote->setValue($status);
        $caseNeedSubForm->status2Note->setValue($status2);
    }
}
