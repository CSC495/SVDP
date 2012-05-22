<?php

class Application_Model_Member_ScheduleForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function __construct($users)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'action' => $baseUrl->baseUrl(App_Resources::MEMBER) . '/editSchedule',
            'method' => 'post',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array('viewScript' => 'form/schedule-form.phtml')),
                'Form',
            ),
        ));

        $this->addSubForm(
            new Application_Model_Member_ScheduleRecordListSubForm($users),
            'scheduleRecordList'
        );

        $this->addElement('submit', 'submit', array(
            'label' => 'Submit',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));
    }

    public function preValidate($data)
    {
        $this->scheduleRecordList->preValidate($data);
    }

    public function handleAddRemoveEntries($data)
    {
        return $this->scheduleRecordList->handleAddRemoveRecords($data);
    }

    public function getChangedEntries()
    {
        return $this->scheduleRecordList->getChangedRecords();
    }

    public function getRemovedEntries()
    {
        return $this->scheduleRecordList->getRemovedRecords();
    }

    public function setEntries($entries)
    {
        $this->scheduleRecordList->setRecords($entries);
    }
}
