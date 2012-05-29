<?php
class Application_Model_Member_CaseForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_id;

    /**
     * `true` if a limit violation has occurred, otherwise `false`.
     *
     * @var bool
     */
    private $_limitViolation;

    public function __construct($clientId)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'id' => 'caseneedForm',
            'action' => $baseUrl->baseUrl(App_Resources::MEMBER
                . '/newCase/clientId/' . urlencode($clientId)),
            'method' => 'post',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array('viewScript' => 'form/case-form.phtml')),
                'Form',
            ),
            'class' => 'form-horizontal twocol',
        ));

        $this->addSubForm(
            new Application_Model_Member_CaseNeedRecordListSubForm(),
            'needRecordList'
        );

        $this->addElement('submit', 'caseneedSubmit', array(
            'label' => 'Create Case',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));
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

        $action = preg_replace('|/skipLimitCheck/1|', '', $this->getAction());

        if ($limitViolation) {
            $action .= '/skipLimitCheck/1';
            $this->caseneedSubmit->setAttrib('class', 'btn btn-danger');
        } else {
            $this->caseneedSubmit->setAttrib('class', 'btn btn-success');
        }

        $this->setAction($action);

        return $this;
    }

    public function preValidate($data)
    {
        $this->needRecordList->preValidate($data);
    }

    public function handleAddRemoveNeeds($data)
    {
        return $this->needRecordList->handleAddRemoveRecords($data);
    }

    public function getChangedNeeds()
    {
        return $this->needRecordList->getChangedRecords();
    }

    public function addEmptyNeed()
    {
        return $this->needRecordList->addEmptyRecord();
    }
}
