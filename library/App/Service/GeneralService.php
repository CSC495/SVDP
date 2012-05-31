<?php
/**
 *@package ServiceFilePackage
*/
/**
 *General Service File
 *
 *Holds methods that all controllers will need to use
 *@package ServiceFilePackage
 */
class App_Service_GeneralService {
    /**
     *Holds connection to DB
    */
    private $_db;
    
    /**
     *Creates a connection to the DB available to the class
     *@return void
    */
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /**
     *Gets schedule information from database.
     *
     *Returns an array of populated ScheduleEntry objects representing the schedule entries in the
     *database, ordering them by start_date.
     *
     *@return array of Application_Model_Impl_SheduleEntry
    */
    public function getScheduleEntries()
    {
        $select = $this->_db->select()
            ->from(array('s' => 'schedule'), array('s.week_id', 's.start_date', 's.user_id'))
            ->join(
                array('u' => 'user'),
                's.user_id = u.user_id',
                array('u.first_name', 'u.last_name')
            )
            ->order('s.start_date', 'u.first_name', 'u.last_name', 's.user_id', 's.week_id');

        $results = $this->_db->fetchAssoc($select);
        return $this->buildScheduleEntryModels($results);
    }
    
    /**
     *Returns the number of pending check requests
     *
     *@return int number of pending check requests
    */
    public function getNumPendingCheckRequests()
    {
        $select = $this->_db->select()
                ->from('check_request',
                       array('totalReqs' => 'COUNT(*)'))
                ->where("status = 'P'");
        $results = $this->_db->fetchRow($select);
        return $results['totalReqs'];
    }
    
    /**
     *Returns the total amount of the pending check requests.
     *
     *@return int total amount of the pending check requests
    */
    public function getPendingCheckRequestsAmount(){
        $select = $this->_db->select()
                ->from('check_request',
                       array('totalAmount' => 'SUM(amount)'))
                ->where("status = 'P'");
        $results = $this->_db->fetchRow($select);
        return $results['totalAmount'];
    }
    
    /**
     *Builds a ScheduleEntry object.
     *
     *Creates a ScheduleEntry object, populates it with the data in the given associative array
     *, and returns the object
     *
     *@param mixed[string]
     *@return Application_Model_Impl_SheduleEntry
    */
    private function buildScheduleEntryModels($dbResults)
    {
        $scheduleEntries = array();

        foreach ($dbResults as $dbResult) {
            $user = new Application_Model_Impl_User();
            $user
                ->setUserId($dbResult['user_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name']);

            $scheduleEntry = new Application_Model_Impl_ScheduleEntry();
            $scheduleEntry
                ->setId($dbResult['week_id'])
                ->setStartDate($dbResult['start_date'])
                ->setUser($user);

            $scheduleEntries[$dbResult['week_id']] = $scheduleEntry;
        }

        return $scheduleEntries;
    }
}
