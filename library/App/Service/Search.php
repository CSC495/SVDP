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

    private static $_STREET_ADDR_DIRS = array('n', 'north', 'w', 'west', 's', 'south', 'e', 'east');

    private static $_STREET_ADDR_SUFFIXES = array(
        'ave', 'av', 'avenue', 'cir', 'cr', 'circle', 'ct', 'court', 'ln', 'lane', 'lp', 'loop',
        'pkwy', 'pky', 'parkway', 'pl', 'place', 'rd', 'road', 'sq', 'square', 'st', 'street',
        'trl', 'trail'
    );

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
            ->where("CONCAT_WS(' ', c.first_name, c.last_name) LIKE ?", $likeName);
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

    /**
     * Given a first name (optional), a last name (optional), and an address (required), retrieves a
     * list of possible clients duplicating that information.
     *
     * @param string|null $firstName
     * @param string|null $lastName
     * @return Application_Model_Impl_Client[]
     */
    public function getSimilarClients($addr, $firstName = null, $lastName = null)
    {
        $likeStreetName = '%'
                        . App_Escaping::escapeLike(self::extractStreetName($addr->getStreet()))
                        . '%';
        $likeFirstName  = ($firstName !== null)
                        ? '%' . App_Escaping::escapeLike($firstName) . '%'
                        : '';
        $likeLastName   = ($lastName !== null)
                        ? '%' . App_Escaping::escapeLike($lastName) . '%'
                        : '';
        $select         = $this->_db->select()
            ->union(array(
                $this->initClientSelect(true)
                    ->where(
                        $this->_db->quoteInto('a.street LIKE ? AND ', $likeStreetName)
                      . $this->_db->quoteInto('a.city = ? AND ', $addr->getCity())
                      . $this->_db->quoteInto('a.state = ?', $addr->getState())
                    ),
                $this->initClientSelect(true)
                    ->where(
                        $this->_db->quoteInto('c.first_name LIKE ? OR ', $likeFirstName)
                      . $this->_db->quoteInto('c.last_name LIKE ?', $likeLastName)
                    ),
            ));
        $results       = $this->_db->fetchAssoc($select);

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
            ->where("CONCAT_WS(' ', c.first_name, c.last_name) LIKE ? "
                  . "OR CONCAT_WS(' ', c2.first_name, c2.last_name) LIKE ?", $likeName, $likeName);
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

    private function initClientSelect($noOrder = false)
    {
        $select = $this->_db->select()
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
            ->where('h.current_flag = 1');

        if (!$noOrder) {
            $this->orderClientSelect($select);
        }

        return $select;
    }

    private function orderClientSelect(Zend_Db_Select $select)
    {
        return $select->order(array('c.last_name', 'c.first_name', 'c.client_id'));
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
            ->from(array('r' => 'check_request'), array(
                'r.checkrequest_id',
                'r.request_date',
                'r.status',
            ))
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

            if ($dbResult['do_not_help_reason'] !== null) {
                $doNotHelp = new Application_Model_Impl_DoNotHelp();
                $doNotHelp->setReason($dbResult['do_not_help_reason']);
            } else {
                $doNotHelp = null;
            }

            $client = new Application_Model_Impl_Client();
            $client
                ->setId($dbResult['client_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setCellPhone($dbResult['cell_phone'])
                ->setHomePhone($dbResult['home_phone'])
                ->setWorkPhone($dbResult['work_phone'])
                ->setCurrentAddr($addr)
                ->setDoNotHelp($doNotHelp);

            $clients[] = $client;
        }

        return $clients;
    }

    private function buildCaseModels($dbResults)
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

    private function buildCheckReqModels($dbResults)
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

    /**
     * Given a full street address, e.g., "30 N. Brainard St.", returns a best guess at the street
     * name embedded therein, e.g., "Brainard".
     *
     * Test cases:
     *
     * * `extractStreetName('30 N. Brainard Street') === 'Brainard'
     * * `extractStreetName('30 N. Brainard St.') === 'Brainard'
     * * `extractStreetName('30 N Brainard St') === 'Brainard'
     * * `extractStreetName('30 N Brainard') === 'Brainard'
     * * `extractStreetName('30 Brainard') === 'Brainard'
     * * `extractStreetName('29W365 Army Trail Rd') === 'Army Trail'
     * * `extractStreetName('29W365 Army Trail') === 'Army'
     * * `extractStreetName('123 North West St') === 'West'
     * * `extractStreetName('123 North Ave') === 'North'
     * * `extractStreetName('Cow') === 'Cow'
     * * `extractStreetName('') === ''
     *
     * @param string $street
     * @return string
     */
    public static function extractStreetName($street)
    {
        $chunks = explode(' ', $street);

        // Only proceed if there's at least one address chunk to work with.
        if ($chunks) {
            // If there's a trailing address chunk that looks like one of the usual USPS suffixes,
            // set a flag to check later on.
            $lastChunk = strtolower(preg_replace('/[^A-Za-z0-9]/', '', end($chunks)));
            $hasSuffix = in_array($lastChunk, self::$_STREET_ADDR_SUFFIXES);

            // Try to strip house numbers and directions from the beginning of the address.
            while (count($chunks) - $hasSuffix > 1) {
                // Before examining this street address chunk, strip nonalphanumeric characters and make
                // the string lowercase.
                reset($chunks);
                $firstChunk = strtolower(preg_replace('/[^A-Za-z0-9]/', '', current($chunks)));

                // If a chunk begins with a number, then it's probably a house number---remove it.
                if ($firstChunk !== '' && ctype_digit($firstChunk[0])) {
                    array_shift($chunks);
                    continue;
                }

                // If a chunk matches a direction name or abbreviation, remove it.
                if (in_array($firstChunk, self::$_STREET_ADDR_DIRS)) {
                    array_shift($chunks);
                    continue;
                }

                break;
            }

            // Finally, remove the suffix chunk, if any.
            if ($hasSuffix) {
                array_pop($chunks);
            }
        }

        // Return the remaining street address chunks, joined by spaces.
        return implode(' ', $chunks);
    }
}
