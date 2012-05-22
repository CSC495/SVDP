<?php

class Application_Model_Member_ScheduleRecordListSubForm extends App_Form_RecordListSubFormAbstract
{

    private $_users;

    public function __construct($users)
    {
        parent::__construct(array(
            'namespace' => 'schedule',
            'labels' => array('Week Begins', 'Member Name'),
            'narrow' => true,
            'legend' => 'Edit the parish schedule:',
            'description' => 'Please submit your changes for additions and removals to take place.',
            'addRecordMsg' => 'Add Another Entry',
            'noRecordsMsg' => 'No members are currently scheduled.',
            'submitMsg' => 'Submit',
        ));

        $this->_users = $users;
    }

    protected function initSubForm($entrySubForm)
    {
        $entrySubForm->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

        $entrySubForm->addElement('text', 'startDate', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must choose a start date.'),
                )),
                array('Date', true, array(
                    'format' => 'MM/dd/yyyy',
                    'messages' => array(
                        'dateInvalidDate' => 'Must be properly formatted.',
                        'dateFalseFormat' => 'Must be a valid date.',
                    ),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'Addon',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
            ),
            'maxlength' => 10,
            'class' => 'span2 date',
        ));

        $entrySubForm->addElement('select', 'userId', array(
            'multiOptions' => $this->_users,
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must choose a member.'),
                )),
                array('InArray', true, array(
                    'haystack' => array_keys($this->_users),
                    'strict' => true,
                    'messages' => array('notInArray' => 'Must choose a member.'),
                )),
            ),
            'class' => 'span3',
        ));
    }

    protected function getRecord($entrySubForm)
    {
        $user = new Application_Model_Impl_User();
        $user->setUserId(App_Formatting::emptyToNull($entrySubForm->userId->getValue()));

        $entry = new Application_Model_Impl_ScheduleEntry();
        $entry
            ->setId(App_Formatting::emptyToNull($entrySubForm->id->getValue()))
            ->setStartDate(App_Formatting::unformatDate($entrySubForm->startDate->getValue()))
            ->setUser($user);

        return $entry;
    }

    protected function setRecord($entrySubForm, $entry)
    {
        $entrySubForm->id->setValue($entry->getId());
        $entrySubForm->startDate->setValue(App_Formatting::formatDate($entry->getStartDate()));
        $entrySubForm->userId->setValue($entry->getUser()->getUserId());
    }
}
