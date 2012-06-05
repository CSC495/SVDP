<?php

class Application_Model_Member_CaseVisitRecordListSubForm
    extends App_Form_RecordListSubFormAbstract {

    private $_users;

    public function __construct(array $users, $readOnly)
    {
        parent::__construct(array(
            'namespace' => 'casevisit',
            'labels' => array(
                'Date',
                'Miles',
                'Hours',
                'Primary Member',
                'Secondary Member (Optional)',
            ),
            'readOnly' => $readOnly,
            'legend' => 'Case visits:',
            'addRecordMsg' => 'Add Another Visit',
            'noRecordsMsg' => 'No visits listed.',
            'submitMsg' => 'Submit',
        ));

        $this->_users = $users;
    }

    protected function initSubForm($caseVisitSubForm)
    {
        $caseVisitSubForm->addElementPrefixPath('App_Validate', 'App/Validate/', 'validate');

        $caseVisitSubForm->addElement('hidden', 'id', array(
            'decorators' => array(
                'ViewHelper',
                array('HtmlTag', array('tag' => 'td', 'openOnly' => true)),
            ),
        ));

        $caseVisitSubForm->addElement('text', 'date', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must choose a visit date.'),
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

        $caseVisitSubForm->addElement('text', 'miles', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must enter miles.'),
                )),
                array('Float', true, array(
                    'messages' => array('notFloat' => 'Must be a number.'),
                )),
                array('GreaterThanOrEqualTo', true, array(
                    'min' => 0,
                    'messages' => array('notGreaterThan' => 'Must not be negative.'),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td')),
            ),
            'maxlength' => 11,
            'class' => 'span1',
        ));

        $caseVisitSubForm->addElement('text', 'hours', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'Must enter hours.'),
                )),
                array('Float', true, array(
                    'messages' => array('notFloat' => 'Must be an number.'),
                )),
                array('GreaterThan', true, array(
                    'min' => 0,
                    'messages' => array('notGreaterThan' => 'Must be positive.'),
                )),
            ),
            'decorators' => array(
                'ViewHelper',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td')),
            ),
            'maxlength' => 11,
            'class' => 'span1',
        ));

        $caseVisitSubForm->addElement('select', 'primaryUserId', array(
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

        $caseVisitSubForm->addElement('select', 'secondaryUserId', array(
            'multiOptions' => $this->_users,
            'validators' => array(
                array('InArray', true, array(
                    'haystack' => array_keys($this->_users),
                    'strict' => true,
                    'messages' => array('notInArray' => 'Must choose a member.'),
                )),
            ),
            'class' => 'span3',
        ));
    }

    protected function getRecord($caseVisitSubForm)
    {
        $caseVisit = new Application_Model_Impl_CaseVisit();
        $caseVisit
            ->setId(App_Formatting::emptyToNull($caseVisitSubForm->id->getValue()))
            ->setDate(App_Formatting::unformatDate($caseVisitSubForm->date->getValue()))
            ->setMiles(App_Formatting::emptyToNull($caseVisitSubForm->miles->getValue()))
            ->setHours(App_Formatting::emptyToNull($caseVisitSubForm->hours->getValue()))
            ->setVisitors(array());

        if ($caseVisitSubForm->primaryUserId->getValue() !== '') {
            $user = new Application_Model_Impl_User();
            $user->setUserId($caseVisitSubForm->primaryUserId->getValue());
            $caseVisit->addVisitor($user);
        }

        if ($caseVisitSubForm->secondaryUserId->getValue() !== '') {
            $user = new Application_Model_Impl_User();
            $user->setUserId($caseVisitSubForm->secondaryUserId->getValue());
            $caseVisit->addVisitor($user);
        }

        return $caseVisit;
    }

    protected function setRecord($caseVisitSubForm, $caseVisit)
    {
        $caseVisitSubForm->id->setValue($caseVisit->getId());
        $caseVisitSubForm->date->setValue(App_Formatting::formatDate($caseVisit->getDate()));
        $caseVisitSubForm->miles->setValue($caseVisit->getMiles());
        $caseVisitSubForm->hours->setValue($caseVisit->getHours());

        $visitors = $caseVisit->getVisitors();

        if ($visitor = array_shift($visitors)) {
            $caseVisitSubForm->primaryUserId->setValue($visitor->getUserId());
        }

        if ($visitor = array_shift($visitors)) {
            $caseVisitSubForm->secondaryUserId->setValue($visitor->getUserId());
        }
    }
}
