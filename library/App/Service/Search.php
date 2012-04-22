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

    private $_clientColumns = array(
        'c.client_id',
        'c.first_name',
        'c.last_name',
        'c.cell_phone',
        'c.home_phone',
        'c.work_phone',
    );

    private $_householdColumns = array();

    private $_addrColumns = array(
        'a.address_id',
        'a.street',
        'a.apt',
        'a.city',
        'a.state',
        'a.zipcode',
    );

    private $_caseColumns = array(
        's.case_id',
        's.opened_date',
    );

    private $_caseNeedColumns = array(
        'need_list' => 'GROUP_CONCAT(n.need SEPARATOR ", ")',
        'total_amount' => 'SUM(n.amount)',
    );

    private $_clientOrderColumns = array('c.last_name', 'c.first_name', 'c.client_id');

    private $_caseOrderColumns = array('c.last_name', 'c.first_name', 'c.client_id', 'n.case_id');

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
     * Retrieve a list of clients whose first or last names match the specified query.
     *
     * @param string $name
     * @return Application_Model_Client[]
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
     * @return Application_Model_Client[]
     */
    public function getClientsByAddr($addr)
    {
        $likeAddr = '%' . App_Escaping::escapeLike($addr) . '%';
        $select  = $this->initClientSelect()
            ->where(
                'a.street LIKE ? OR a.apt LIKE ? OR a.city LIKE ? OR a.state LIKE ?'
                    . ' OR a.zipcode LIKE ?',
                $likeAddr,
                $likeAddr,
                $likeAddr,
                $likeAddr,
                $likeAddr
            );
        $results = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    /**
     * Retrieve a list of clients whose cell, home, or work phone numbers match the specified query.
     *
     * @param string $phone
     * @return Application_Model_Client[]
     */
    public function getClientsByPhone($phone)
    {
        $select  = $this->initClientSelect()
            ->where(
                'c.cell_phone = ? OR c.home_phone = ? OR c.work_phone = ?',
                $phone,
                $phone,
                $phone
            );
        $results = $this->_db->fetchAssoc($select);

        return $this->buildClientModels($results);
    }

    /* Case search methods: */

    /**
     * Retrieve a list of open cases for the specified user.
     *
     * @param string $userId
     * @return Application_Model_Case[]
     */
    public function getOpenCasesByUserId($userId)
    {
        $select  = $this->initCaseSelect()
            ->where('s.opened_user_id = ?', $userId)
            ->where('s.status = "Open"');
        $results = $this->_db->fetchAssoc($select);

        return $this->buildCaseModels($results);
    }

    /* Internal helper methods: */

    private function initClientSelect()
    {
        return $this->_db->select()
            ->from(array('c' => 'client'), $this->_clientColumns)
            ->join(
                array('h' => 'household'),
                'c.client_id = h.mainclient_id OR c.client_id = h.spouse_id',
                $this->_householdColumns
            )
            ->join(
                array('a' => 'address'),
                'a.address_id = h.address_id',
                $this->_addrColumns)
            ->where('h.current_flag = 1')
            ->order($this->_clientOrderColumns);
    }

    private function initCaseSelect()
    {
        return $this->_db->select()
            ->from(array('c' => 'client'), $this->_clientColumns)
            ->join(
                array('h' => 'household'),
                'c.client_id = h.mainclient_id OR c.client_id = h.spouse_id',
                $this->_householdColumns
            )
            ->join(
                array('s' => 'client_case'),
                'h.household_id = s.household_id',
                $this->_caseColumns
            )
            ->join(
                array('n' => 'case_need'),
                's.case_id = n.case_id',
                $this->_caseNeedColumns
            )
            ->group('n.case_id')
            ->order($this->_caseOrderColumns);
    }

    private function buildClientModels($dbResults)
    {
        $clients = array();

        foreach ($dbResults as $dbResult) {
            $addr = new Application_Model_Addr();
            $addr
                ->setId($dbResult['address_id'])
                ->setStreet($dbResult['street'])
                ->setApt($dbResult['apt'])
                ->setCity($dbResult['city'])
                ->setState($dbResult['state'])
                ->setZip($dbResult['zipcode']);

            $client = new Application_Model_Client();
            $client
                ->setId($dbResult['client_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setCellPhone($dbResult['cell_phone'])
                ->setHomePhone($dbResult['home_phone'])
                ->setWorkPhone($dbResult['work_phone'])
                ->setCurrentAddr($addr);

            $clients[] = $client;
        }

        return $clients;
    }

    public function buildCaseModels($dbResults)
    {
        $cases = array();

        foreach ($dbResults as $dbResult) {
            $client = new Application_Model_Client();
            $client
                ->setId($dbResult['client_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setCellPhone($dbResult['cell_phone'])
                ->setHomePhone($dbResult['home_phone'])
                ->setWorkPhone($dbResult['work_phone']);

            $case = new Application_Model_Case();
            $case
                ->setId($dbResult['case_id'])
                ->setOpenedDate($dbResult['opened_date'])
                ->setNeedList($dbResult['need_list'])
                ->setTotalAmount($dbResult['total_amount'])
                ->setClient($client);

            $cases[] = $case;
        }

        return $cases;
    }
}
