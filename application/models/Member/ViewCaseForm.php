<?php

class Application_Model_Member_ViewCaseForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_readOnly;

    /**
     * `true` if a limit violation has occurred, otherwise `false`.
     *
     * @var bool
     */
    private $_limitViolation;

    public function __construct($userId, Application_Model_Impl_Case $case, array $comments,
        array $users, $readOnly)
    {
        $this->_readOnly = $readOnly;

        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'method' => 'post',
            'class' => 'form-horizontal',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/view-case-form.phtml',
                    'action' => $baseUrl->baseUrl(
                        App_Resources::MEMBER . '/viewCase/id/' . urlencode($case->getId())
                    ),
                    'case' => $case,
                    'readOnly' => &$this->_readOnly,
                    'limitViolation' => &$this->_limitViolation,
                )),
            ),
        ));

        if (!$this->_readOnly) {
            $this->addElement('submit', 'closeCase', array(
                'label' => 'Close Case',
                'decorators' => array('ViewHelper'),
                'class' => 'btn btn-danger',
            ));
        }

        $this->addSubForm(
            new Application_Model_Member_CaseNeedRecordListSubForm(
                true,
                $this->_readOnly,
                $case->getId()
            ),
            'needRecordList'
        );

        $this->addSubForm(
            new Application_Model_Member_CaseVisitRecordListSubForm($users, $this->_readOnly),
            'visitRecordList'
        );

        $this->addSubForm(
            new Application_Model_Member_CommentsSubForm($userId, $comments),
            'commentsSubForm'
        );
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
        $this->needRecordList->setLimitViolation($limitViolation);
        return $this;
    }

    public function preValidate(array $data)
    {
        $this->needRecordList->preValidate($data);
        $this->visitRecordList->preValidate($data);
    }

    public function isValid($data)
    {
        if ($this->isCloseCaseRequest($data)) {
            return true;
        }

        if ($this->isChangeNeedsRequest($data)) {
            return $this->needRecordList->isValid($data);
        }

        if ($this->isChangeVisitsRequest($data)) {
            return $this->visitRecordList->isValid($data);
        }

        if ($this->commentsSubForm->isAddCommentRequest($data)) {
            return $this->commentsSubForm->isValid($data);
        }

        return false;
    }

    public function isCloseCaseRequest(array $data)
    {
        return isset($data['closeCase']);
    }

    public function isChangeNeedsRequest(array $data)
    {
        return isset($data['caseneedRecordsRemoved']);
    }

    public function isChangeVisitsRequest(array $data)
    {
        return isset($data['casevisitRecordsRemoved']);
    }

    public function handleAddRemoveRecords(array $data)
    {
        return $this->needRecordList->handleAddRemoveRecords($data)
            || $this->visitRecordList->handleAddRemoveRecords($data);
    }

    public function getChangedNeeds()
    {
        return $this->needRecordList->getChangedRecords();
    }

    public function getRemovedNeeds()
    {
        return $this->needRecordList->getRemovedRecords();
    }

    public function setNeeds(array $needs)
    {
        $this->needRecordList->setRecords($needs);
    }

    public function getChangedVisits()
    {
        return $this->visitRecordList->getChangedRecords();
    }

    public function getRemovedVisits()
    {
        return $this->visitRecordList->getRemovedRecords();
    }

    public function setVisits(array $visits)
    {
        $this->visitRecordList->setRecords($visits);
    }

    public function getAddedComment(array $data)
    {
        if ($this->commentsSubForm->isAddCommentRequest($data)) {
            return $this->commentsSubForm->getComment();
        }

        return null;
    }
}
