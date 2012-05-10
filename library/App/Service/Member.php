<?php

class App_Service_Member
{

    private $_db;

    public function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /******* PUBLIC GET QUERIES *******/

    //Given a client_id returns a Client object populated with all client information
    public function getClientById($clientId)
    {
        $select = $this->_db->select()
            ->from(array('c' => 'client'), array(
                'c.client_id',
                'c.created_user_id',
                'c.first_name',
                'c.last_name',
                'c.other_name',
                'c.marriage_status',
                'c.birthdate',
                'c.ssn4',
                'c.cell_phone',
                'c.home_phone',
                'c.work_phone',
                'c.created_date',
                'c.member_parish',
                'c.veteran_flag',
            ))
            ->join(
                array('h' => 'household'),
                'c.client_id = h.mainclient_id OR c.client_id = h.spouse_id',
                array()
            )
            ->joinLeft(
                array('c2' => 'client'),
                'c2.client_id <> c.client_id '
                . 'AND (c2.client_id = h.mainclient_id OR c2.client_id = h.spouse_id)',
                array(
                    'spouse_id' => 'c2.client_id',
                    'spouse_first_name' => 'c2.first_name',
                    'spouse_birthdate' => 'c2.birthdate',
                    'spouse_ssn4' => 'c2.ssn4',
                )
            )
            ->join(
                array('a' => 'address'),
                'a.address_id = h.address_id',
                array(
                    'a.address_id',
                    'a.street',
                    'a.apt',
                    'a.city',
                    'a.state',
                    'a.zipcode',
                    'a.reside_parish',
                )
            )
            ->joinLeft(
                array('d' => 'do_not_help'),
                'c.client_id = d.client_id',
                array('do_not_help_reason' => 'd.reason')
            )
            ->where('h.current_flag = 1')
            ->where('c.client_id = ?', $clientId);

        $results = $this->_db->fetchRow($select);
        return $this->buildClientModel($results);
    }

    //Given a client_id returns an array of Householder objects populated with information
    //of each household member
    public function getHouseholdersByClientId($clientId)
    {
        $select = $this->_db->select()
            ->from(array('m' => 'hmember'), array(
                'm.hmember_id',
                'm.first_name',
                'm.last_name',
                'm.relationship',
                'm.birthdate',
                'm.left_date',
            ))
            ->join(
                array('h' => 'household'),
                'm.household_id = h.household_id',
                array()
            )
            ->where('h.current_flag = 1')
            ->where('h.mainclient_id = ? OR h.spouse_id = ?', $clientId, $clientId)
            ->order(array('m.last_name', 'm.first_name', 'm.hmember_id'));

        $results = $this->_db->fetchAssoc($select);
        return $this->buildHouseholderModels($results);
    }

    //Given a client_id returns an array of populated Employer objects representing
    //client's employment history
    public function getEmployersByClientId($clientId)
    {
        $select = $this->_db->select()
            ->from(array('e' => 'employment'), array(
                'e.employment_id',
                'e.company',
                'e.position',
                'e.start_date',
                'e.end_date',
            ))
            ->where('e.client_id = ?', $clientId)
            ->order(array(
                'ISNULL(e.end_date) DESC',
                'e.end_date DESC',
                'e.start_date DESC',
                'e.employment_id',
            ));

        $results = $this->_db->fetchAssoc($select);
        return $this->buildEmployerModels($results);
    }
    
    //Given a client_id returns an array of populated Case objects for each case
    //associated with the client, returns all cases Opened and Closed
    public function getCasesByClient($clientId){
        $select = $this->db->select()
			->from(array('cc' => 'client_case'),
				     array('caseID' => 'cc.case_id',
                                           'addByName' => 'cc.opened_user_id', 
					   'dateRequested' => 'cc.opened_date',
					   'status' => 'cc.status',
					   'hours' => 'hours',
					   'miles' => 'miles'))
			->joinInner(array('h' => 'household'), 'cc.household_id = h.household_id')
			->joinInner(array('c' => 'client'), 'c.client_id = h.mainclient_id')
			->joinInner(array('cn' => 'case_need'), 'cc.case_id = cn.case_id')
			->joinInner(array('u' => 'user'), 'u.user_id = cc.opened_user_id')
			->joinLeft(array('cv' => 'case_visit'), 'cc.case_id = cv.case_id')
			->group('cc.case_id')
			->where('c.client_id = ?', $client_id);
		$results = $this->db->fetchAll($select);
		return $this->buildCaseModels($results);
    }

    //Returns an array of populated User objects who are currently active
    public function getActiveMembers()
    {
        $select = $this->_db->select()
            ->from(array('u' => 'user'), array(
                'u.user_id',
                'u.first_name',
                'u.last_name',
            ))
            ->where('u.active_flag = ?', 1)
            ->where('u.role = ?', 'M')
            ->order(array('u.last_name', 'u.first_name', 'u.user_id'));

        $results = $this->_db->fetchAssoc($select);
        return $this->buildUserModels($results);
    }

    //Returns an array of populated ScheduleEntry objects representing all schedule entries
    //in the database, ordering them by start_date
    public function getScheduleEntries()
    {
        $select = $this->_db->select()
            ->from(array('s' => 'schedule'), array('s.week_id', 's.start_date', 's.user_id'))
            ->order('s.start_date', 's.user_id', 's.week_id');

        $results = $this->_db->fetchAssoc($select);
        return $this->buildScheduleEntryModels($results);
    }
    
    //Given a checkrequest_id returns a populated CheckReq object
    public function getCheckReqById($id){
        $select = $this->_db->select()
                ->from('check_request')
                ->where('checkrequest_id = ?', $id);
        $results = $this->_db->fetchRow($select);
        return $this->buildCheckRequestModel($results);
    }
    
    //Given an array of caseneed_ids returns an associative array of CheckReq
    //objects with the id as the key and the CheckReq object as the value
    public function getCheckReqListByNeeds($needIdArr){
        $requests =  array();
        foreach($needIdArr as $id){
            $requests[$id] = $this->getCheckReqByNeed($id);
        }
        return $requests;
    }
    
    //Given a caseneed_id returns a populated CheckReq object
    //NOTE: the object returned has User and SigneeUser as only the ids, not User objects
    //can change if need be
    public function getCheckReqByNeed($needId){
        $select = $this->_db->select()
                ->from('check_request')
                ->where('caseneed_id = ?', $needId);
        $results = $this->_db->fetchRow($select);
        return $this->buildCheckRequestModel($results);
    }
    
    /****** PUBLIC CREATE/INSERT QUERIES ******/
    
    //Given a Client object, Householder object array, and an Employer object array
    //creates a new client in database, inserts all relavent information
    public function createClient($client, $householders, $employers)
    {
        $this->_db->beginTransaction();

        try {
            // Insert the new client.
            $this->_db->insert('client', $this->disassembleClientModel($client));;
            $client->setId($this->_db->lastInsertId());

            // There's just no helping some people...
            if ($client->isDoNotHelp()) {
                $this->_db->insert('do_not_help', array(
                    'client_id' => $client->getId(),
                    'create_user_id' => $client->getUserId(),
                    'added_date' => $client->getCreatedDate(),
                    'reason' => $client->getDoNotHelpReason(),
                ));
            }

            // If married, insert the new client's spouse.
            if ($client->isMarried()) {
                $this->_db->insert('client', $this->disassembleClientModel($client->getSpouse()));
                $client->getSpouse()->setId($this->_db->lastInsertId());
            }

            // Insert the new client's address.
            // XXX: Why does the `address` table even have a `client_id` column?
            $addrData = $this->disassembleAddrModel($client->getCurrentAddr());
            $addrData['client_id'] = $client->getId();
            $this->_db->insert('address', $addrData);
            $client->getCurrentAddr()->setId($this->_db->lastInsertId());

            // Insert a household for the new client (and possibly his/her spouse).
            $this->_db->insert('household', array(
                'address_id' => $client->getCurrentAddr()->getId(),
                'mainclient_id' => $client->getId(),
                'spouse_id' => $client->isMarried() ? $client->getSpouse()->getId() : null,
                'current_flag' => 1,
            ));
            $householdId = $this->_db->lastInsertId();

            $this->createHouseholders($householdId, $householders);

            $this->createEmployers($client->getId(), $employers);

            $this->_db->commit();
        } catch (Exception $ex) {
            $this->_db->rollBack();
            throw $ex;
        }

        return $client;
    }
    
    //Creates a new case entry in database, passed Case object
    //populated with all information except id
    //Returns same Case object with id added
    public function createCase($case){
        $this->_db->beginTransaction();
        try{
            $caseData = $this->disassembleCaseModel($case);
            $this->_db->insert('client_case', $caseData);
            $case->setId($this->_db->lastInsertId());
            
            $case = $this->insertNeeds($case);
            $case = $this->insertVisits($case);
            $this->_db->commit();
            return $case;
        }catch(Exception $ex){
            $this->_db->rollBack();
            throw $ex;
        }
    }
    
    //Creates a new check request entry in database, passed a
    //fully populated CheckRequest object except for id
    //Returns the same object with id added
    public function createCheckRequest($request){
        $this->_db->beginTransaction();
        try{
            $reqData = $this->disassembleCheckRequestModel($request);
            $this->_db->insert('check_request', $reqData);
            $request->setId($this->_db->lastInsertId());
            $this->_db->commit();
            return $request;
        }catch(Exception $ex){
            $this->_db->rollBack();
            throw $ex;
        }
    }
    
    /****** PUBLIC EDIT/UPDATE/DELETE QUERIES  ******/
    
    //Updates all information relevant to the given client
    //Passed a fully populated Client object, a string of the
    //client's marriage status IF it changed, null otherwise, and a boolean flag
    //indicating if the client has moved
    public function editClient($client, $marriageStatus, $movingFlag){
        $this->_db->beginTransaction();
        try{
            $clientData = $this->disassembleClientModel($client);
            $addrData = $this->disassembleAddrModel($client->getCurrentAddr());
            
            //Update Client data in client table
            $where = $this->_db->quoteInto('client_id = ?', $client->getId());
            $this->_db->update('client', $clientData, $where);
            
            //If the client moved or had a change in marital status creates a new household, defaults
            //with values of old household
            if($marriageStatus || $movingFlag){
                $this->createNewHousehold($this->getCurrentAddress($client->getId()), $client->getId());
            }
            
            //If the client moved create new address; else edit the existing entry with submitted data
            if($movingFlag)
                $this->createNewAddress($addrData, $client->getId());
            else
                $this->editAddress($addrData, $this->getCurrentAddress($client->getId()));
                
            //If the client had a change in marital status they either got married or divorced
            if($marriageStatus){
                //Client got married, add thier spouse to client and new spouse id to household
                if($client->isMarried())
                    $this->clientMarriage($client);
                //Client got divorced, change household to not married & create new household for spouse
                else
                    $this->clientDivorce($client->getId());
            //Client did not have change in marital status, may have changed spouse's information
            }else{
                $this->editSpouse($client->getId(), $client->getSpouse());
            }
            //Update any changes to existing employment records or create new ones
            $this->editEmployment($client->getEmployment(), $client->getId());
            
            //Update any changes to existing hmember records or create new ones
            //$this->editHouseHolders($client->getHouseMembers(), $this->getCurrentHouseholdId($client->getId()));
            $this->_db->commit();
        }catch(Exception $ex){
            $this->_db->rollBack();
            throw $ex;
        }
    }
    
    //Updates all information relevant to the given case with nested objects that
    //may or may not be in the database
    //Passed a case object fully populated
    //Returns the same object with all nested objects entered in the databse and given ids
    public function editCase($case){
        //Update the information in the client_case table
        $caseData = $this->disassembleCaseModel($case);
        $caseData['case_id'] = $case->getId();
        $where = $this->_db->quoteInto('case_id = ?', $case->getId());
        $this->_db->update('client_case', $caseData, $where);
        
        //Update case needs
        $case = $this->insertNeeds($case);
        
        //Update case visits
        $this->insertVisits($case);
        return $case;
    }
    
    //Updates all information relevant to the given check request
    //Passed a CheckRequest object fully populated
    public function editCheckRequest($request){
        $reqData = $this->disassembleCheckRequestModel($request);
        $where = $this->_db->quoteInto('checkrequest_id = ?', $request->getId());
        $this->_db->update('check_request', $reqData, $where);
    }
    
    //Given a ScheduleEntry object updates entry information or adds it to database
    //Returns same object with id set if added to database
    public function changeScheduleEntry($scheduleEntry)
    {
        if ($scheduleEntry->getId() === null) {
            $this->_db->insert('schedule', $this->disassembleScheduleEntryModel($scheduleEntry));
            $scheduleEntry->setId($this->_db->lastInsertId());
        } else {
            $this->_db->update(
                'schedule',
                $this->disassembleScheduleEntryModel($scheduleEntry),
                $this->_db->quoteInto('week_id = ?', $scheduleEntry->getId())
            );
        }

        return $scheduleEntry;
    }
    
    //Given an array of ScheduleEntry objects deletes all entries in database indicated
    //in the array
    public function removeScheduleEntries($scheduleEntries)
    {
        if (!$scheduleEntries) {
            return;
        }

        $scheduleEntryIds = array();
        foreach ($scheduleEntries as $scheduleEntry) {
            $scheduleEntryIds[] = $scheduleEntry->getId();
        }

        $this->_db->delete('schedule', $this->_db->quoteInto(
            'week_id IN (?)',
            $scheduleEntryIds
        ));
    }
    
    /****** PRIVATE GET QUERIES  ******/
    
    //Fetches the household_id of the given client's current household
    private function getCurrentHouseholdId($clientId){
        $select = $this->_db->select()
                ->from('household', 'household_id')
                ->where('mainclient_id = ?', $clientId)
                ->where('current_flag = ?', '1');
        $results = $this->_db->fetchRow($select);
        return $results['household_id'];
    }
    
    //Fetches the address_id of the given client's current address
    private function getCurrentAddress($clientId){
        $select = $this->_db->select()
                ->from('household', 'address_id')
                ->where('mainclient_id = ?', $clientId)
                ->where('current_flag = 1');
        $results = $this->_db->fetchRow($select);
        return $results['address_id'];
    }
    
    //Fetches the spouse_id of the given client's spouse
    //returns null if they are not married
    private function getSpouseId($clientId){
        $select = $this->_db->select()
                    ->from('household', 'spouse_id')
                    ->where('mainclient_id = ?', $clientId)
                    ->where('current_flag = ?', '1');
        $results = $this->_db->fetchRow($select);
        if($results)
            return $results['spouse_id'];
        else
            return null;
    }
    
    //Fetches an array of populated CaseNeed objects relevant to the given case
    private function getNeedsByCase($caseId){
        $needs = array();
        $select = $this->_db->select()
                    ->from(array('cn' => 'case_need'),
                           array('caseNeedId' => 'cn.caseneed_id',
                           'need',
                           'amount'))
                    ->where('cn.case_id = ?', $caseId);
        $results = $this->_db->fetchAll($select);
        
        foreach($results as $row){
            $need = new Application_Model_Impl_CaseNeed();
            $need->setCaseNeedId($row['caseNeedId']);
            $need->setNeed($row['need']);
            $need->setAmount($row['amount']);
            $needs[] = $need;
        }
        return $needs;
    }
    
    //Fethces an array of populated CaseVisit objects relevant to the given case
    private function getVisitsByCase($caseId){
        $visits = array();
        $select = $this->_db->select()
                ->from(array('cv' => 'case_visit'),
                       array('visitId' => 'visit_id',
                             'visitDate' => 'visit_date',
                             'miles',
                             'hours'))
                ->where('cv.case_id = ?', $caseId);
        $results = $this->_db->fetchAll($select);
        
        foreach($results as $row){
            $visit = new Application_Model_Impl_CaseVisit();
            $visit->setVisitId($row['visitId']);
            $visit->setVisitDate($row['visitDate']);
            $visit->setMiles($row['miles']);
            $visit->setHours($row['hours']);
            $visits[] = $visit;
        }
        return $visits;
    }

    /****** PRIVATE CREATE/INSERT QUERIES  ******/
    
    private function createHouseholders($householdId, $householders)
    {
        foreach ($householders as $householder) {
            $householderData = $this->disassmebleHouseholderModel($householder);
            $householderData['household_id'] = $householdId;

            $this->_db->insert('hmember', $householderData);
        }
    }

    private function createEmployers($clientId, $employers)
    {
        foreach ($employers as $employer) {
            $employerData = $this->disassembleEmployerModel($employer);
            $employerData['client_id'] = $clientId;

            $this->_db->insert('employment', $employerData);
        }
    }
    
    //Creates a new address in database and changes the household address_id to id
    //of new address
    private function createNewAddress($addrData, $clientId){
        $newHouseId = $this->_db->lastInsertId();
        
        $addrData['client_id'] = $clientId;
        $this->_db->insert('address', $addrData);
        
        $newAddId = $this->_db->lastInsertId();
        
        $where = $this->_db->quoteInto('household_id = ?', $newHouseId);
        $change = array('address_id' => $newAddId);
        $this->_db->update('household', $change, $where);
    }
    
    //Creates a new household for the given client using the given address
    //Sets all other households associated with the client to not current
    //Returns the household_id of the newly created household entry
    private function createNewHousehold($addressId, $clientId){
        $spouseId = $this->getSpouseId($clientId);
        
        $where = $this->_db->quoteInto('mainclient_id = ?', $clientId);
        $change = array('current_flag' => '0');
        $this->_db->update('household', $change, $where);
        $houseData = array(
                    'address_id' => $addressId,
                    'mainclient_id' => $clientId,
                    'spouse_id' => $spouseId,
                    'current_flag' => '1');
        $this->_db->insert('household', $houseData);
        return $this->_db->lastInsertId();
    }
    
    //Given a Case object with nested CaseNeed objects updates existing
    //case needs and adds new needs to database
    //Returns the Case object with updated nested CaseNeeds (new CaseNeeds have Id's)
    //as well as the total amount of all CaseNeeds
    private function insertNeeds($case){
        $needs = $case->getNeedList();
        $caseId = $case->getId();
        $newNeeds = array();
        $totalAmount = 0;
        foreach($needs as $need){
            $needData = $this->disassembleCaseNeedModel($need);
            if($need->getCaseNeedId()){
                $this->updateCaseNeed($needData, $need->getCaseNeedId());
                $totalAmount += $needData['amount'];
                $newNeeds[] = $need;
            }else{
                $needData['case_id'] = $caseId;
                $totalAmount += $needData['amount'];
                $this->_db->insert('case_need', $needData);
                $need->setCaseNeedId($this->_db->lastInsertId());
                $newNeeds[] = $need;
            }
        }
        $case->setTotalAmount($totalAmount);
        $case->setNeedList($newNeeds);
        return $case;
    }
    
    //Given a Case object with nested CaseVisit objects updates CaseVisit information of visits
    //in the database and adds new visits. 
    //Returns the same Case object with all nested CaseVisit updated (i.e added visits have ids)
    private function insertVisits($case){
        $visits = $case->getVisits();
        $caseId = $case->getId();
        $newVisits = array();
        foreach($visits as $visit){
            $visitData = $this->disassembleCaseVisitModel($visit);
            if($visit->getVisitId()){
                $this->updateCaseVisit($visitData, $visit->getVisitId());
                $newVisits[] = $visit;
            }else{
                $visitData['case_id'] = $caseId;
                $this->_db->insert('case_visit', $visitData);
            
                //Insert individual visitors in case_visitors table
                $newVisitId = $this->_db->lastInsertId();
                $this->insertVisitors($visit->getVisitors(), $newVisitId);
            
                $visit->setVisitId($newVisitId);
                $newVisits[] = $visit;
            }
        }
        $case->setVisits($newVisits);
        return $case;
    }
    
    //Given an array of User objects representing visitors of the given visit, updates
    //visitor information associated with the given visit
    private function insertVisitors($visitors, $visitId){
        foreach($visitors as $visitor){
            $visitorData = array(
                'visit_id' => $visitId,
                'user_id' => $visitor->getUserId(),
            );
            $this->_db->insert('case_visitors', $visitorData);
        }
    }
    
    /****** PRIVATE EDIT/UPDATE QUERIES  ******/
    
    //Updates the address information with the given data at the entry given by the id
    private function editAddress($addrData, $addrId){
        $where = $this->_db->quoteInto('address_id = ?', $addrId);
        $this->_db->update('address', $addrData, $where);
    }
    
    //Updates the spouse information in the client table with the given information
    //within the Client object
    private function editSpouse($clientId, $spouse){
        $spouseId = $this->getSpouseId($clientId);
        if($spouseId){
            $spouseData = $this->disassembleClientModel($spouse);
            $where = $this->_db->quoteInto('client_id = ?', $spouseId);
            $this->_db->update('client', $spouseData, $where);
        }
    }
    
    //Updates all employment information with the given array of Employer objects at
    //the entry with the given id
    private function editEmployment($employment, $clientId){
        $newEmploy = array();
        foreach($employment as $job){
            if(!$job->getId()){
                $newEmploy[] = $job;
            }else{
                $jobData = $this->disassembleEmployerModel($job);
                $where = $this->_db->quoteInto('employment_id = ?', $job->getId());
                $this->_db->update('employment', $jobData, $where);
            }
        }
        $this->createEmployers($clientId, $newEmploy);
    }
    
    //Updates information of all hmembers already in the database
    //and adds those that are new
    private function editHouseHolders($householders, $clientId){
        $newHolders = array();
        foreach($householders as $holder){
            if(!$holder->getId()){
                $newHolders[] = $holder;
            }else{
                $holderData = $this->disassmebleHouseholderModel($holder);
                $where = $this->_db->quoteInto('hmember_id = ?', $holder->getId());
                $this->_db->update('hmember', $holderData, $where);
            }
        }
        $this->createHouseholders($this->getCurrentHouseholdId(), $newHolders);
    }
    
    //Updates all client and client's ex-spouse information in
    //client, household, and address tables
    private function clientDivorce($clientId){
        $spouseId = $this->getSpouseId($clientId);
        $newHouseId = $this->getCurrentHouseholdId($clientId);
        
        //Update spouse_id for client's new household
        $where = $this->_db->quoteInto('household_id = ?', $newHouseId);
        $change = array('spouse_id' => NULL);
        $this->_db->update('household', $change, $where);
        
        //Update client's ex-spouse's marriage status
        $where = $this->_db->quoteInto('client_id = ?', $spouseId);
        $change = array('marriage_status' => 'Divorced');
        $this->_db->update('client', $change, $where);
        
        //Create new address & household for client's ex-spouse
        //the new address information (i.e street, city, etc.) will be null
        $this->createNewHousehold(NULL, $spouseId);
        $this->createNewAddress(array(), $spouseId);
    }
    
    //Updates client's household to reflect marriage and adds client's
    //spouse to client table
    //Assumes $_spouse in Client is a Client object
    private function clientMarriage($client){
        //Insert the client's spouse in client table
        $spouseData = $this->disassembleClientModel($client->getSpouse());
        $spouseData['marriage_status'] = 'Married';
        $spouseData['created_user_id'] = $client->getUserId();
        $this->_db->insert('client', $spouseData);
        $newSpouseId = $this->_db->lastInsertId();
        
        //Update client's household to include spouse
        $newHouseId = $this->getCurrentHouseholdId($client->getId());
        $where = $this->_db->quoteInto('household_id = ?', $newHouseId);
        $change = array('spouse_id' => $newSpouseId);
        $this->_db->update('household', $change, $where);
    }
    
    //Updates information in case_need table given an array of updated information,
    //typically produced by disassembler
    private function updateCaseNeed($needData, $needId){
        $where = $this->_db->quoteInto('caseneed_id = ?', $needId);
        $this->_db->update('case_need', $needData, $where);
    }
    
    //Updates information in case_visit table given an array of updated information,
    //typically produced by disassembler
    private function updateCaseVisit($visitData, $visitId){
        $where = $this->_db->quoteInto('visit_id = ?', $visitId);
        $this->_db->update('case_visit', $visitData, $where);
    }
    
    /****** IMPL OBJECT BUILDERS  ******/
    
    private function buildClientModel($dbResult)
    {
        $addr = new Application_Model_Impl_Addr();
        $addr
            ->setId($dbResult['address_id'])
            ->setStreet($dbResult['street'])
            ->setApt($dbResult['apt'])
            ->setCity($dbResult['city'])
            ->setState($dbResult['state'])
            ->setZip($dbResult['zipcode'])
            ->setParish($dbResult['reside_parish']);

        if ($dbResult['spouse_first_name'] !== null) {
            $spouse = new Application_Model_Impl_Client();
            $spouse->setId($dbResult['spouse_id'])
                   ->setFirstName($dbResult['spouse_first_name'])
                   ->setBirthDate($dbResult['spouse_birthdate'])
                   ->setSsn4($dbResult['spouse_ssn4']);
        } else {
            $spouse = null;
        }

        $client = new Application_Model_Impl_Client();
        $client
            ->setId($dbResult['client_id'])
            ->setUserId($dbResult['created_user_id'])
            ->setFirstName($dbResult['first_name'])
            ->setLastName($dbResult['last_name'])
            ->setOtherName($dbResult['other_name'])
            ->setMaritalStatus($dbResult['marriage_status'])
            ->setBirthDate($dbResult['birthdate'])
            ->setSsn4($dbResult['ssn4'])
            ->setCellPhone($dbResult['cell_phone'])
            ->setHomePhone($dbResult['home_phone'])
            ->setWorkPhone($dbResult['work_phone'])
            ->setCreatedDate($dbResult['created_date'])
            ->setParish($dbResult['member_parish'])
            ->setVeteran($dbResult['veteran_flag'])
            ->setSpouse($spouse)
            ->setCurrentAddr($addr)
            ->setDoNotHelpReason($dbResult['do_not_help_reason']);

        return $client;
    }

    private function buildHouseholderModels($dbResults)
    {
        $householders = array();

        foreach ($dbResults as $dbResult) {
            $householder = new Application_Model_Impl_Householder();
            $householder
                ->setId($dbResult['hmember_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name'])
                ->setRelationship($dbResult['relationship'])
                ->setBirthDate($dbResult['birthdate'])
                ->setDepartDate($dbResult['left_date']);

            $householders[$dbResult['hmember_id']] = $householder;
        }

        return $householders;
    }

    private function buildEmployerModels($dbResults)
    {
        $employers = array();

        foreach ($dbResults as $dbResult) {
            $employer = new Application_Model_Impl_Employer();
            $employer
                ->setId($dbResult['employment_id'])
                ->setCompany($dbResult['company'])
                ->setPosition($dbResult['position'])
                ->setStartDate($dbResult['start_date'])
                ->setEndDate($dbResult['end_date']);

            $employers[$dbResult['employment_id']] = $employer;
        }

        return $employers;
    }
    
    private function buildCaseModels($results){
        $cases = array();
	foreach($results as $row){
	    $case = new Application_Model_Impl_Case();
	    $case
                ->setId($results['caseID'])
		->setOpenedDate($results['dateRequested'])
                ->setStatus($results['status'])
                ->setOpenedUserId($results['addByName'])
                ->setVisits($this->getVisitsByCase($results['caseID']))
                ->setCaseNeeds($this->getNeedsByCase($results['caseID']));
	    $cases[] = $case;
	}
	return $cases;
    }

    private function buildUserModels($dbResults)
    {
        $users = array();

        foreach ($dbResults as $dbResult) {
            $user = new Application_Model_Impl_User();
            $user
                ->setUserId($dbResult['user_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name']);

            $users[$dbResult['user_id']] = $user;
        }

        return $users;
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
    
    //User and SigneeUser are the ids of the users, can change to objects if need be
    private function buildCheckRequestModel($results){
        $request = new Application_Model_Impl_CheckReq();
        $address = new Application_Model_Impl_Addr();
        $address
            ->setStreet($results['street'])
            ->setCity($results['city'])
            ->setState($results['state'])
            ->setZip($results['zipcode']);
        $request
            ->setId($results['checkrequest_id'])
            ->setCaseNeedId($results['caseneed_id'])
            ->setUser($results['user_id'])
            ->setRequestDate($results['request_date'])
            ->setAmount($results['amount'])
            ->setComment($results['comment'])
            ->setSigneeUser($results['signee_userid'])          
            ->setCheckNumber($results['check_number'])
            ->setIssueDate($results['issue_date'])
            ->setAccountNumber($results['account_number'])
            ->setPayeeName($results['payee_name'])
            ->setAddress($address)
            ->setPhone($results['phone'])
            ->setContactFirstName($results['contact_fname'])
            ->setContactLastName($results['contact_lname']);
        return $request;
    }
    
    /****** IMPL OBJECT DISASSEMBLERS  ******/

    private function disassembleClientModel($client)
    {
        return array(
            'created_user_id' => $client->getUserId(),
            'first_name' => $client->getFirstName(),
            'last_name' => $client->getLastName(),
            'other_name' => $client->getOtherName(),
            'marriage_status' => $client->getMaritalStatus(),
            'birthdate' => $client->getBirthDate(),
            'ssn4' => $client->getSsn4(),
            'cell_phone' => $client->getCellPhone(),
            'home_phone' => $client->getHomePhone(),
            'work_phone' => $client->getWorkPhone(),
            'created_date' => $client->getCreatedDate(),
            'member_parish' => $client->getParish(),
            'veteran_flag' => (int)$client->isVeteran(),
        );
    }
    
    private function disassembleCaseModel($case){
        return array(
            'household_id' => $case->getHouseholdId(),
            'opened_user_id' => $case->getOpenedUserId(),
            'opened_date' => $case->getOpenedDate(),
            'status' => $case->getStatus()
        );
    }

    private function disassembleAddrModel($addr)
    {
        return array(
            'street' => $addr->getStreet(),
            'apt' => $addr->getApt(),
            'city' => $addr->getCity(),
            'state' => $addr->getState(),
            'zipcode' => $addr->getZip(),
            'reside_parish' => $addr->getParish(),
        );
    }

    private function disassmebleHouseholderModel($householder)
    {
        return array(
            'first_name' => $householder->getFirstName(),
            'last_name' => $householder->getLastName(),
            'relationship' => $householder->getRelationship(),
            'birthdate' => $householder->getBirthDate(),
            'left_date' => $householder->getDepartDate(),
        );
    }

    private function disassembleEmployerModel($employer)
    {
        return array(
            'company' => $employer->getCompany(),
            'position' => $employer->getPosition(),
            'start_date' => $employer->getStartDate(),
            'end_date' => $employer->getEndDate(),
        );
    }

    private function disassembleCaseNeedModel($need){
        return array(
            'need' => $need->getNeed(),
            'amount' => $need->getAmount(),
        );
    }
    
    private function disassembleCaseVisitModel($visit){
        return array(
            'visit_date' => $visit->getVisitDate(),
            'miles' => $visit->getMiles(),
            'hours' => $visit->getHours(),
        );
    }
    
    private function disassembleCheckRequestModel($request){
        return array(
            'caseneed_id' => $request->getCaseNeedId(),
            'user_id' => $request->getSigneeUser()->getUserId(),
            'request_date' => $request->getRequestDate(),
            'amount' => $request->getAmount(),
            'comment' => $request->getComment(),
            'signee_userid' => $request->getSigneeUser()->getUserId(),
            'check_number' => $request->getCheckNumber(),
            'issue_date' => $request->getIssueDate(),
            'account_number' => $request->getAccountNumber(),
            'payee_name' => $request->getPayeeName(),
            'street' => $request->getAddress()->getStreet(),
            'city' => $request->getAddress()->getCity(),
            'state' => $request->getAddress()->getState(),
            'zipcode' => $request->getAddress()->getZip(),
            'phone' => $request->getPhone(),
            'contact_fname' => $request->getContactFirstName(),
            'contact_lname' => $request->getContactLastName()
        );
    }
    

    private function disassembleScheduleEntryModel($scheduleEntry)
    {
        return array(
            'week_id' => $scheduleEntry->getId(),
            'start_date' => $scheduleEntry->getStartDate(),
            'user_id' => $scheduleEntry->getUser()->getUserId(),
        );
    }
}
