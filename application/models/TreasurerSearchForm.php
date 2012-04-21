<?php

class Application_Model_TreasurerSearchForm extends Application_Model_SearchForm
{

    private $additionalTypes = array(
        Application_Model_SearchForm::TYPE_CLIENT_ID => array(
            'label' => 'Client ID',
        ),
        Application_Model_SearchForm::TYPE_CASE_ID => array(
            'label' => 'Case ID',
        ),
        Application_Model_SearchForm::TYPE_CHECK_REQ_ID => array(
            'label' => 'Check Request ID',
            'validators' => array(
                array('Db_RecordExists', true, array(
                    'table' => 'check_request',
                    'field' => 'checkrequest_id',
                    'messages' => array(
                        'noRecordFound' => 'No check request was found for that ID.'
                    ),
                )),
            ),
        ),
    );

    public function __construct($options = null)
    {
        parent::__construct('treasurer', $this->additionalTypes, $options);
    }
}
