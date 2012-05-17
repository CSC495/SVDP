<?php

class Application_Model_Member_ViewCaseForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_readOnly;

    public function __construct(Application_Model_Impl_Case $case, array $users)
    {
        parent::__construct(array(
            'action' => self::makeActionUrl($case->getId()),
            'method' => 'post',
            'class' => 'form-horizontal twocol',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/view-case-form.phtml',
                    'case' => $case,
                )),
                'Form',
            ),
        ));

        $this->_readOnly = ($case->getStatus() === 'Closed');

        $this->addElement('text', 'caseId', array(
            'label' => 'Case ID',
            'readonly' => true,
            'ignore' => true,
            'value' => $case->getId(),
        ));

        $this->addElement('text', 'caseStatus', array(
            'label' => 'Case status',
            'readonly' => true,
            'ignore' => true,
            'value' => $case->getStatus(),
        ));

        $this->addElement('text', 'dateOpened', array(
            'label' => 'Date opened',
            'readonly' => true,
            'ignore' => true,
            'value' => App_Formatting::formatDate($case->getOpenedDate()),
        ));

        $this->addElement('text', 'dateOpened', array(
            'label' => 'Date opened',
            'readonly' => true,
            'ignore' => true,
            'value' => App_Formatting::formatDate($case->getOpenedDate()),
        ));

        $this->addElement('text', 'openingUser', array(
            'label' => 'Opening member',
            'readonly' => true,
            'ignore' => true,
            'value' => $case->getOpenedUser()->getFullName(),
        ));

        if (!$this->_readOnly) {
            $this->addElement('submit', 'closeCase', array(
                'label' => 'Close Case',
                'decorators' => array('ViewHelper'),
                'class' => 'btn btn-danger',
            ));
        }

        $this->addSubForm(
            new Application_Model_Member_CaseVisitRecordListSubForm($users, $this->_readOnly),
            'visitRecordList'
        );

        $this->visitRecordList->setRecords($case->getVisits());
    }

    private static function makeActionUrl($id)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
        return $baseUrl->baseUrl(App_Resources::MEMBER . '/viewCase'
            . (($id !== null) ? '/id/' . urlencode($id) : ''));
    }
}
