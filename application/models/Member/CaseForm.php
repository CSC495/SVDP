<?php
class Application_Model_Member_CaseForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_id;

    public function __construct($clientId)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
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

        $this->addElement('submit', 'submit', array(
            'label' => 'Open Case',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));
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
