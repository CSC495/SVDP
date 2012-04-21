<?php

class Application_Model_MemberSearchForm extends Application_Model_SearchForm
{

    private $additionalTypes = array(
        Application_Model_SearchForm::TYPE_CLIENT_ID => array(
            'label' => 'Client ID',
            'validators' => array(
                array('Db_RecordExists', true, array(
                    'table' => 'client',
                    'field' => 'client_id',
                    'messages' => array(
                        'noRecordFound' => 'No client was found for that ID.'
                    ),
                )),
            ),
        ),
        Application_Model_SearchForm::TYPE_CASE_ID => array(
            'label' => 'Case ID',
            'validators' => array(
                array('Db_RecordExists', true, array(
                    'table' => 'client_case',
                    'field' => 'case_id',
                    'messages' => array(
                        'noRecordFound' => 'No case was found for that ID.'
                    ),
                )),
            ),
        ),
    );

    public function __construct($options = null)
    {
        parent::__construct('member', $this->additionalTypes, $options);
    }
}
