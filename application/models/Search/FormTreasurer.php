<?php

/**
 * Search form specialized for check request searches performed by a treasurer user.
 */
class Application_Model_Search_FormTreasurer extends Application_Model_Search_FormAbstract
{

    /**
     * Search query types specific to treasurer users.
     *
     * @var array
     */
    private $_additionalTypes = array(
        Application_Model_Search_FormAbstract::TYPE_CLIENT_ID => array(
            'label' => 'Client ID',
        ),
        Application_Model_Search_FormAbstract::TYPE_CASE_ID => array(
            'label' => 'Case ID',
        ),
        Application_Model_Search_FormAbstract::TYPE_CHECK_REQ_ID => array(
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

    /**
     * Initializes a new instance of the `Application_Model_FormTreasurer` class.
     */
    public function __construct()
    {
        parent::__construct('treasurer', $this->_additionalTypes);
    }
}
