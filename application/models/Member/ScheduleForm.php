<?php

class Application_Model_Member_ScheduleForm extends Zend_Form
{

    public function __construct($users)
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this
            ->setAction($baseUrl->baseUrl(App_Resources::MEMBER) . '/editSchedule')
            ->setMethod('post')
            ->setDecorators(array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/schedule-form.phtml',
                )),
                array('form', array('class' => 'member form-horizontal')),
            ));

        $this->addSubForm(
            new Application_Model_Member_ScheduleRecordListSubForm($users),
            'scheduleRecordList'
        );

        $this->addElement('submit', 'submit', array(
            'label' => 'Submit Changes',
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
}
