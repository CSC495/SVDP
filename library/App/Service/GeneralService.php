<?php

class App_Service_GeneralService {
    private $_db;
    
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    //Returns an array of 12 populated ScheduleEntry objects representing the first 12 schedule entries
    //in the database, ordering them by start_date
    public function getScheduleEntries()
    {
        $select = $this->_db->select()
            ->from(array('s' => 'schedule'), array('s.week_id', 's.start_date', 's.user_id'))
            ->order('s.start_date', 's.user_id', 's.week_id')
            ->limitPage(0, 12);

        $results = $this->_db->fetchAssoc($select);
        return $this->buildScheduleEntryModels($results);
    }
    
    private function buildScheduleEntryModels($dbResults)
    {
        $scheduleEntries = array();

        foreach ($dbResults as $dbResult) {
            $user = new Application_Model_Impl_User();
            $user->setUserId($dbResult['user_id']);

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