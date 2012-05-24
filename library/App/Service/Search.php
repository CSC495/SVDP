<?php

/**
 * Search layer for various actions that perform searches and display a summarized list of
 * clients/cases/check requests. This includes all actions in the search controller, as well as the
 * index actions of the member and treasurer controllers.
 *
 * Note: No methods in this service class should write to the database.
 *
 * Note: Methods in this class only populate the model fields likely to be needed when displaying
 * short summaries of clients, cases, and check request. Complete data (e.g., full address and
 * employment history, amounts broken down by need, etc.) will not be fetched, and any unnecessary
 * model properties will be left set to `null`.
 */
class App_Service_Search
{

    private $_db;

    /**
     * Constructs a new `App_Service_Search` object, retrieving a database connection for future
     * use.
     */
    public function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    /* Client search methods: */

    /**
     * Retrieve a list of all clients.
     *
     * @return Application_Model_Impl_Client[]
     */
    public function getAllClients()
    {
        return $this->buildClientModels($this->_db->fetchAssoc($this->initClientSelect()));
    }

    /**
     * Retrieve a list of clients whose first or last names match the specified query.
     *
     * @param string $name
     * @return Application_Model_Impl_Client[]
     */
    public function getClientsByName($name)
    {
        $likeName = '%' . App_Escaping::escapeLike($name) . '%';
        $select   = $this->initClientSelect()
            ->where('c.last_name LIKE ? OR c.first_name LIKE ?', $likeName, $likeName);
        $results  = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    /**
     * Retrieve a list of clients whose addresses match the specified query.
     *
     * @param string $addr
     * @return Application_Model_Impl_Client[]
     */
    public function getClientsByAddr($addr)
    {
        $likeAddr = '%' . App_Escaping::escapeLike($addr) . '%';
        $select   = $this->initClientSelect()
            ->where(
                'a.street LIKE ? OR a.apt LIKE ? OR a.city LIKE ? OR a.state LIKE ?'
                    . ' OR a.zipcode LIKE ?',
                $likeAddr, $likeAddr, $likeAddr, $likeAddr, $likeAddr
            );
        $results  = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    /**
     * Retrieve a list of clients whose cell, home, or work phone numbers match the specified query.
     *
     * @param string $phone
     * @return Application_Model_Impl_Client[]
     */
    public function getClientsByPhone($phone)
    {
        $likePhone = '%' . App_Escaping::escapeLike($phone) . '%';
        $select    = $this->initClientSelect()
            ->where(
                'c.cell_phone LIKE ? OR c.home_phone LIKE ? OR c.work_phone LIKE ?',
                $likePhone, $likePhone, $likePhone
            );
        $results   = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    /* Case search methods: */

    /**
     * Retrieve a list of open cases for the specified user.
     *
     * @param string $userId
     * @return Application_Model_Impl_Case[]
     */
    public function getOpenCasesByUserId($userId)
    {
        $select  = $this->initCaseSelect()
            ->where('s.opened_user_id = ?', $userId)
            ->where('s.status <> "Closed"');
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCaseModels($results);
    }

    /**
     * Retrieve a list of case history for the specified client.
     *
     * @param string $caseId
     * @return Application_Model_Impl_Case[]
     */
    public function getCasesByClientId($clientId) {
        $select  = $this->initCaseSelect()
            ->where('c.client_id = ?', $clientId);
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCaseModels($results);
    }

    /* Check request search methods: */

    /**
     * Retrieve a list of all check requests.
     *
     * @return Application_Model_Impl_CheckReq[]
     */
    public function getAllCheckReqs()
    {
        return $this->buildCheckReqModels($this->_db->fetchAssoc($this->initCheckReqSelect()));
    }

    /**
     * Retrieve a list of currently open check requests.
     *
     * @return Application_Model_Impl_CheckReq[]
     */
    public function getOpenCheckReqs()
    {
	
		/*$select  = $this->initCaseSelect()
            ->where('s.opened_user_id = ?', $userId)
            ->where('s.status <> "Closed"');
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCaseModels($results);
		*/
		
        // XXX: Check requests don't currently have a status field in the database, so we just
        // return all open check requests. This is not desired behavior, obviously!
        $select  = $this->initCheckReqSelect()
            ->where('r.status <> "I" AND r.status <> "D"');
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCheckReqModels($results);
    }

    /**
     * Retrieve a list of check request whose first or last client names match the specified query.
     *
     * @param string $name
     * @return Application_Model_Impl_CheckReq[]
     */
    public function getCheckReqsByClientName($name)
    {
        $likeName = '%' . App_Escaping::escapeLike($name) . '%';
        $select   = $this->initCheckReqSelect()
            ->where('c.last_name LIKE ? OR c.first_name LIKE ? OR c2.last_name LIKE ?'
                        .' OR c2.first_name LIKE ?',
                    $likeName, $likeName, $likeName, $likeName);
        $results  = $this->_db->fetchAssoc($select);

        return $this->buildCheckReqModels($results);
    }

    /**
     * Retrieve a list of check requests whose client addresses match the specified query.
     *
     * @param string $addr
     * @return Application_Model_Impl_CheckReq[]
     */
    public function getCheckReqsByClientAddr($addr)
    {
        $likeAddr = '%' . App_Escaping::escapeLike($addr) . '%';
        $select  = $this->initCheckReqSelect()
            ->join(array('a' => 'address'), 'a.address_id = h.address_id', array())
            ->where(
                'a.street LIKE ? OR a.apt LIKE ? OR a.city LIKE ? OR a.state LIKE ?'
                    . ' OR a.zipcode LIKE ?',
                $likeAddr, $likeAddr, $likeAddr, $likeAddr, $likeAddr
            );
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCheckReqModels($results);
    }

    /**
     * Retrieve a list of check requests whose client cell, home, or work phone numbers match the
     * specified query.
     *
     * @param string $phone
     * @return Application_Model_Impl_CheckReq[]
     */
    public function getCheckReqsByClientPhone($phone)
    {
        $likePhone = '%' . App_Escaping::escapeLike($phone) . '%';
        $select  = $this->initCheckReqSelect()
            ->where(
                'c.cell_phone LIKE ? OR c.home_phone LIKE ? OR c.work_phone LIKE ?'
                    . ' OR c2.cell_phone LIKE ? OR c2.home_phone = ? OR c2.work_phone = ?',
                $likePhone, $likePhone, $likePhone, $likePhone, $likePhone, $likePhone
            );
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCheckReqModels($results);
    }

    /**
     * Retrieve a list of check requests matching the specified client ID.
     *
     * @param string $clientId
     * @return Application_Model_Impl_CheckReq[]
     */
    public function getCheckReqsByClientId($clientId)
    {
        $select  = $this->initCheckReqSelect()
            ->where('c.client_id = ? OR c2.client_id = ?', $clientId, $clientId);
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCheckReqModels($results);
    }

    /**
     * Retrieve a list of check requests matching the specified case ID.
     *
     * @param string $caseId
     * @return Application_Model_Impl_CheckReq[]
     */
    public function getCheckReqsByCaseId($caseId)
    {
        $select  = $this->initCheckReqSelect()
            ->where('s.case_id = ?', $caseId);
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCheckReqModels($results);
    }

    /* Internal helper methods: */

    private function initClientSelect()
    {
        return $this->_db->select()
            ->from(array('c' => 'client'), array(
                'c.client_id',
                'c.first_name',
                'c.last_name',
                'c.cell_phone',
                'c.home_phone',
                'c.work_phone',
            ))
            ->join(
                array('h' => 'household'),
                'c.client_id = h.mainclient_id OR c.client_id = h.spouse_id',
                array()
            )
            ->join(
                array('a' => 'address'),
                'a.address_id = h.address_id',
                array('a.address_id', 'a.street', 'a.apt', 'a.city', 'a.state', 'a.zipcode')
            )
            ->joinLeft(
                array('d' => 'do_not_help'),
                'c.client_id = d.client_id',
                array('do_not_help_reason' => 'd.reason')
            )
            ->where('h.current_flag = 1')
            ->order(array('c.last_name', 'c.first_name', 'c.client_id'));
    }

    private function initCaseSelect()
    {
        return $this->_db->select()
            ->from(array('s' => 'client_case'), array('s.case_id', 's.opened_date', 's.status'))
            ->join(
                array('n' => 'case_need'),
                's.case_id = n.case_id',
                array(
                    'need_list' => 'GROUP_CONCAT(n.need SEPARATOR ", ")',
                    'total_amount' => 'SUM(n.amount)',
                )
            )
            ->join(
                array('u' => 'user'),
                's.opened_user_id = u.user_id',
                array(
                    'u.user_id',
                    'user_first_name' => 'u.first_name',
                    'user_last_name' => 'u.last_name',
                )
            )
            ->join(array('h' => 'household'), 's.household_id = h.household_id', array())
            ->join(
                array('c' => 'client'),
                'h.mainclient_id = c.client_id',
                array(
                    'c.client_id',
                    'c.first_name',
                    'c.last_name',
                    'c.cell_phone',
                    'c.home_phone',
                    'c.work_phone',
                )
            )
            ->group('n.case_id')
            ->order('s.opened_date DESC', 's.case_id');
    }

    private function initCheckReqSelect()
    {
        return $this->_db->select()
            ->from(array('r' => 'check_request'), array('r.checkrequest_id', 'r.request_date', 'r.status'))
            ->join(
                array('n' => 'case_need'),
                'r.caseneed_id = n.caseneed_id',
                array('n.need', 'n.amount')
            )
            ->join(
                array('s' => 'client_case'),
                'n.case_id = s.case_id',
                array('s.case_id', 's.opened_date')
            )
            ->join(array('h' => 'household'), 's.household_id = h.household_id', array())
            ->join(
                array('c' => 'client'),
                'h.mainclient_id = c.client_id',
                array(
                    'c.client_id',
                    'c.first_name',
                    'c.last_name',
                    'c.cell_phone',
                    'c.home_phone',
                    'c.work_phone',
                )
            )
            ->joinLeft(array('c2' => 'client'), 'h.spouse_id = c2.client_id', array())
            ->order(array(
                'c.last_name',
                'c.first_name',
                'c.client_id',
                's.case_id',
                'r.checkrequest_id',
            ));
    }

    private function buildClientModels($dbResults)
    {
        $clients = array();

        foreach ($dbResults as $dbResult) {
            $addr = new Application_Model_Impl_Addr();
            $addr
                ->setId($dbResult['address_id'])
                ->setStreet($dbResult['street'])
                ->setApt($dbResult['apt'])
                ->setCity($dbResult['city'])
                ->setState($dbResult['state'])
                ->setZip($dbResult['zipcode']);

            $client = new Application_Model_Impl_Client();
            $client
                ->setId($dbResult['client_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setCellPhone($dbResult['cell_phone'])
                ->setHomePhone($dbResult['home_phone'])
                ->setWorkPhone($dbResult['work_phone'])
                ->setCurrentAddr($addr)
                ->setDoNotHelpReason($dbResult['do_not_help_reason']);

            $clients[] = $client;
        }

        return $clients;
    }

    public function buildCaseModels($dbResults)
    {
        $cases = array();

        foreach ($dbResults as $dbResult) {
            $user = new Application_Model_Impl_User();
            $user
                ->setUserId($dbResult['user_id'])
                ->setFirstName($dbResult['user_first_name'])
                ->setLastName($dbResult['user_last_name']);

            $client = new Application_Model_Impl_Client();
            $client
                ->setId($dbResult['client_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setCellPhone($dbResult['cell_phone'])
                ->setHomePhone($dbResult['home_phone'])
                ->setWorkPhone($dbResult['work_phone']);

            $case = new Application_Model_Impl_Case();
            $case
                ->setId($dbResult['case_id'])
                ->setOpenedDate($dbResult['opened_date'])
                ->setStatus($dbResult['status'])
                ->setOpenedUser($user)
                ->setNeedList($dbResult['need_list'])
                ->setTotalAmount($dbResult['total_amount'])
                ->setClient($client);

            $cases[] = $case;
        }

        return $cases;
    }

    public function buildCheckReqModels($dbResults)
    {
        $checkReqs = array();

        foreach ($dbResults as $dbResult) {
            $client = new Application_Model_Impl_Client();
            $client
                ->setId($dbResult['client_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setCellPhone($dbResult['cell_phone'])
                ->setHomePhone($dbResult['home_phone'])
                ->setWorkPhone($dbResult['work_phone']);

            $case = new Application_Model_Impl_Case();
            $case
                ->setId($dbResult['case_id'])
                ->setOpenedDate($dbResult['opened_date'])
                ->setNeedList($dbResult['need'])
                ->setTotalAmount($dbResult['amount'])
                ->setClient($client);

            $checkReq = new Application_Model_Impl_CheckReq();
            $checkReq
                ->setId($dbResult['checkrequest_id'])
                ->setRequestDate($dbResult['request_date'])
                ->setCase($case)
				->setStatus($dbResult['status']);

            $checkReqs[] = $checkReq;
        }

        return $checkReqs;
    }
}
