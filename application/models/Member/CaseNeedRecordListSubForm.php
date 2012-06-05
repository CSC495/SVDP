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

    /**
     * `true` if a limit violation has occurred, otherwise `false`.
     *
     * @var bool
     */
    private $_limitViolation;

    public function __construct($showSubmitChanges = false, $readOnly = false, $caseId = null)
    {
        $this->_readOnly   = $readOnly;
        $this->_caseId     = $caseId;
        $this->_showStatus = ($caseId !== null);

        $labels = $this->_showStatus
                ? array('Status', 'Need', 'Amount', '')
                : array('Need', 'Amount');

        $options = array(
            'namespace' => 'caseneed',
            'labels' => $labels,
            'readOnly' => $readOnly,
            'narrow' => !$this->_showStatus,
            'legend' => 'Case needs:',
            'addRecordMsg' => 'Add Another Need',
            'noRecordsMsg' => 'No needs listed.',
        );

        if ($showSubmitChanges) {
            $options['submitMsg'] = 'Submit';
        } else {
            $options['dirtyMsg'] = '';
        }

        parent::__construct($options);
    }

    public function setDefaults(array $defaults)
    {
        parent::setDefaults($defaults);

        if ($this->_showStatus) {
            foreach ($this->caseneedRecords->getSubForms() as $caseNeedSubForm) {
                $caseNeedSubForm->statusNote->setValue($caseNeedSubForm->status->getValue());
                $caseNeedSubForm->status2Note->setValue($caseNeedSubForm->status2->getValue());
            }
        }

        return $this;
    }

    public function isValid($data)
    {
        foreach ($this->_recordsSubForm->getSubForms() as $caseNeedSubForm) {
            $caseNeedSubForm->amount->addFilter('LocalizedToNormalized');
        }

        return parent::isValid($data);
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
                'FieldSize',
                'ViewHelper',
                'Addon',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
            ),
            'dimension' => 3,
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
                    'messages' => array('notGreaterThan' => 'Must be positive.'),
                )),
            ),
            'maxlength' => 10,
            'dimension' => 2,
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
        $caseNeedSubForm->amount->setValue(App_Formatting::formatCurrency($caseNeed->getAmount()));

        if ($this->_showStatus) {
            $this->updateStatus($caseNeedSubForm, $caseNeed);
        }
    }

    /**
     * Returns `true` if the limit violation flag is set, otherwise returns `false`.
     *
     * @return bool
     */
    public function isLimitViolation()
    {
        return $this->_limitViolation;
    }

    /**
     * Sets a flag determining whether or not a limit violation has occurred. If the flag is set,
     * the next form submission will bypass the limit check, allowing case creation anyway.
     *
     * @param bool $limitViolation
     * @return self
     */
    public function setLimitViolation($limitViolation)
    {
        $this->_limitViolation = $limitViolation;
        $this->setSubmitDanger($limitViolation);
        return $this;
    }

    private function updateStatus($caseNeedSubForm, $caseNeed)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        $referralOrCheckReq = $caseNeed->getReferralOrCheckReq();

        if ($referralOrCheckReq instanceof Application_Model_Impl_Referral) {
            $this->setSubFormReadOnly($caseNeedSubForm, true);

            $referral = $referralOrCheckReq;

            $status  = '<span class="label label-info">Referred</span>';
            $status2 = '<abbr title="'
                     . htmlspecialchars($referral->getReason())
                     . '">'
                     . 'Referral: '
                     . htmlspecialchars($referral->getReferredTo())
                     . ' ('
                     . htmlspecialchars(App_Formatting::formatDate($referral->getDate()))
                     . ')'
                     . '</abbr>';
        } else if ($referralOrCheckReq instanceof Application_Model_Impl_CheckReq) {
            $this->setSubFormReadOnly($caseNeedSubForm, true);

            $checkReq        = $referralOrCheckReq;
            $viewCheckReqUrl = $baseUrl->baseUrl(App_Resources::TREASURER . '/view/id/'
                . urlencode($checkReq->getId()));

            $status2 = '<a href="' . htmlspecialchars($viewCheckReqUrl) . '">';

            switch ($checkReq->getStatus()) {
            case 'I':
                $status   = '<span class="label label-success">Issued</span>';
                $status2 .= 'Issued: '
                          . htmlspecialchars(App_Formatting::formatDate($checkReq->getIssueDate()));
                break;

            case 'D':
                $status   = '<span class="label label-important">Denied</span>';
                $status2 .= 'Denied';
                if ($checkReq->getIssueDate() !== null) {
                    $status2 .= ': '
                              . htmlspecialchars(App_Formatting::formatDate(
                                  $checkReq->getIssueDate()));
                }
                break;

            default:
                $status   = '<span class="label label-warning">Pending</span>';
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
            );

            $status  = '<span class="label label-important">Added</span>';
            if (!$this->_readOnly) {
                $status2 = '<a href="'
                         . htmlspecialchars($newReferralUrl)
                         . '" class="btn btn-info">Referral</a>'
                         . ' <a href="'
                         . htmlspecialchars($newCheckReqUrl)
                         . '" class="btn btn-info">Req. Check</a>';
            } else {
                $status2 = '';
            }
        }

        $caseNeedSubForm->status->setValue($status);
        $caseNeedSubForm->status2->setValue($status2);

        $caseNeedSubForm->statusNote->setValue($status);
        $caseNeedSubForm->status2Note->setValue($status2);
    }
}
