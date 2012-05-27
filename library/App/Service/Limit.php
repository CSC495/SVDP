<?php

/**
 * Service class for retrieving totals that allow controller code to flag violations of parish
 * limits on number of cases per client and total monetary needs per case.
 */
class App_Service_Limit
{

    /**
     * Database adapter for service methods.
     *
     * @var Zend_Db_Adapter_Abstract
     */
    private $_db;

    /**
     * Constructs a new `App_Service_Limit` object.
     */
    public function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

    /**
     * Queries the database to determine how many cases are associated with the specified client ID
     * in any household. (This includes current and former spouses' cases.)
     *
     * Returns an associative array with two keys: `lifetimeCases`, which represents the number of
     * cases associated with the given client, and `pastYearCases`, which represents the number of
     * cases associated with the given client that were opened in the past one year period.
     *
     * @param string $clientId
     * @return array
     */
    public function getPastCaseTotal($clientId)
    {
        $this->_db->setFetchMode(Zend_Db::FETCH_ASSOC);

        $results = $this->_db->fetchRow(
            $this->_db->select()
                ->from(array('s' => 'client_case'), array(
                    'lifetimeCases' => 'COUNT(*)',
                    'pastYearCases' =>
                        'COUNT(IF(CURDATE() - INTERVAL 1 YEAR <= s.opened_date, 1, NULL))',
                ))
                ->join(array('h' => 'household'), 's.household_id = h.household_id', array())
                ->where('? IN (h.mainclient_id, h.spouse_id)', $clientId)
        );

        $results['lifetimeCases'] = (int) $results['lifetimeCases'];
        $results['pastYearCases'] = (int) $results['pastYearCases'];

        return $results;
    }

    /**
     * Queries the database to determine how much money the parish has paid out or intends to pay
     * out for the client with the given ID. This total equals the sum of all that client's case
     * needs for which there exists a check request which has not been denied.
     *
     * @param string $clientId
     * @return double
     */
    public function getPastNeedTotal($clientId)
    {
        return (double) $this->_db->fetchOne(
            $this->_db->select()
            ->from(array('n' => 'case_need'), array('IFNULL(SUM(n.amount), 0)'))
            ->join(array('s' => 'client_case'), 'n.case_id = s.case_id', array())
            ->join(array('h' => 'household'), 's.household_id = h.household_id', array())
            ->where(
                'EXISTS ('
              . $this->_db->select()
                    ->from(array('t' => 'check_request'), array('(1)'))
                    ->where('t.caseneed_id = n.caseneed_id')
                    ->where('t.status <> "D"')
              . ')'
            )
            ->where('? IN (h.mainclient_id, h.spouse_id)', $clientId)
        );
    }
}
