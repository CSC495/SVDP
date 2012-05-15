<?php
class Application_Model_Member_CaseForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_id;

    public function __construct($id = null)
    {
        $this->_id = $id;

        parent::__construct(array(
            'action' => $this->makeActionUrl(),
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

        /*$this->addSubForm(
            new Application_Model_Member_CaseVisitRecordListSubForm(),
            'visitRecordList'
        );

        $this->addSubForm(
            new Application_Model_Member_ReferralRecordListSubForm(),
            'referralRecordList'
        );*/
    }

    private function makeActionUrl()
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
        return $baseUrl->baseUrl(App_Resources::MEMBER . '/editCase'
            . (($this->_id !== null) ? '/id/' . urlencode($this->_id) : ''));
    }

    public function preValidate($data)
    {
        $this->needRecordList->preValidate($data);
    }

    public function handleAddRemoveRecords($data)
    {
        return $this->needRecordList->handleAddRemoveRecords($data);
    }

    public function getChangedRecords()
    {
        return $this->needRecordList->getChangedRecords();
    }

    public function getRemovedNeeds()
    {
        return $this->needRecordList->getRemovedRecords();
    }

    public function setNeeds($needs)
    {
        $this->needRecordList->setRecords($needs);
    }
}
