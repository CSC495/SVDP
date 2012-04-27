<?php

/**
 * Search form specialized for client searches performed by a member user.
 */
class Application_Model_Search_SearchFormMember extends Application_Model_Search_SearchFormAbstract
{

    /**
     * Search query types specific to member users.
     *
     * @var array
     */
    private $_additionalTypes = array(
        Application_Model_Search_SearchFormAbstract::TYPE_CLIENT_ID => array(
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
        Application_Model_Search_SearchFormAbstract::TYPE_CASE_ID => array(
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

    /**
     * Initializes a new instance of the `Application_Model_SearchFormMember` class.
     */
    public function __construct()
    {
        parent::__construct('member', $this->_additionalTypes);
    }
}
