<?php

class App_Service_Member
{

    private $_db;

    public function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }

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
		return $this->BuildClientCases($results);
    }
    public function createClient($client, $householders, $employers) {
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
    
    public function createCase($case){
        $this->_db->beginTransaction();
        try{
            $caseData = $this->disassembleCaseModel($case);
            $this->_db->insert('client_case', $caseData);
            $newCaseId = $this->_db->lastInsertId('client_case');
            
            $case->setCaseNeeds($this->insertNeeds($case->getNeedList(), $newCaseId));
            $case->setVisits($this->insertVisits($case->getVisits(), $newCaseId));
            $this->_db->commit();
            return $case;
        }catch(Exception $ex){
            $this->_db->rollBack();
            throw $ex;
        }
    }
    
    public function editClient($client, $marriageFlag, $movingFlag){
        $this->_db->beginTransaction();
        try{
            $clientData = $this->disassembleClientModel($client);
            $addrData = $this->disassembleAddrModel($client->getCurrentAddr());
            
            //Update Client data in client table
            $where = $this->_db->quoteInto('client_id = ?', $client->getId());
            $this->_db->update('client', $clientData, $where);
            
            //If the client moved or had a change in marital status creates a new household, defaults
            //with values of old household
            if($marriageFlag || $movingFlag){
                $this->createNewHousehold($this->getCurrentAddress($client->getId()), $client->getId());
            }
            
            //If the client moved create new address; else edit the existing entry with submitted data
            if($movingFlag)
                $this->createNewAddress($addrData, $client->getId());
            else
                $this->editAddress($addrData, $this->getCurrentAddress($client->getId()));
                
            //If the client had a change in marital status they either got married or divorced
            if($marriageFlag){
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
            //$this->editHouseHolders($client->getHouseMembers(), $this->getCurrentHousehold($client->getId()));
            $this->_db->commit();
        }catch(Exception $ex){
            $this->_db->rollBack();
            throw $ex;
        }
    }

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
            ->setMarried($dbResult['marriage_status'])
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

            $householders[] = $householder;
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

            $employers[] = $employer;
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

    private function disassembleClientModel($client)
    {
        return array(
            'created_user_id' => $client->getUserId(),
            'first_name' => $client->getFirstName(),
            'last_name' => $client->getLastName(),
            'other_name' => $client->getOtherName(),
            'marriage_status' => (int)$client->isMarried(),
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
    
    private function getCurrentHousehold($clientId){
        $select = $this->_db->select()
                ->from('household', 'household_id')
                ->where('mainclient_id = ?', $clientId)
                ->where('current_flag = ?', '1');
        $results = $this->_db->fetchRow($select);
        return $results['household_id'];
    }
    
    private function getCurrentAddress($clientId){
        $select = $this->_db->select()
                ->from('household', 'address_id')
                ->where('mainclient_id = ?', $clientId)
                ->where('current_flag = 1');
        $results = $this->_db->fetchRow($select);
        return $results['address_id'];
    }
    
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
    
    private function editAddress($addrData, $addrId){
        $where = $this->_db->quoteInto('address_id = ?', $addrId);
        $this->_db->update('address', $addrData, $where);
    }
    
    private function editSpouse($clientId, $spouse){
        $spouseId = $this->getSpouseId($clientId);
        if($spouseId){
            $spouseData = $this->disassembleClientModel($spouse);
            $where = $this->_db->quoteInto('client_id = ?', $spouseId);
            $this->_db->update('client', $spouseData, $where);
        }
    }
    
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
        $this->createHouseholders($clientId, $this->getCurrentHousehold());
    }

    private function clientDivorce($clientId){
        $spouseId = $this->getSpouseId($clientId);
        $newHouseId = $this->getCurrentHousehold($clientId);
        
        //Update spouse_id for client's new household
        $where = $this->_db->quoteInto('household_id = ?', $newHouseId);
        $change = array('spouse_id' => NULL);
        $this->_db->update('household', $change, $where);
        
        //Update client's ex-spouse's marriage status
        $where = $this->_db->quoteInto('client_id = ?', $spouseId);
        $change = array('marriage_status' => '0');
        $this->_db->update('client', $change, $where);
        
        //Create new address & household for client's ex-spouse
        $this->createNewHousehold(NULL, $spouseId);
        $this->createNewAddress(array(), $spouseId);
    }
    //Assumes $_spouse in Client is a Client object
    private function clientMarriage($client){
        //Insert the client's spouse in client table
        $spouseData = $this->disassembleClientModel($client->getSpouse());
        $spouseData['marriage_status'] = '1';
        $spouseData['created_user_id'] = $client->getUserId();
        $this->_db->insert('client', $spouseData);
        $newSpouseId = $this->_db->lastInsertId();
        
        //Update client's household to include spouse
        $newHouseId = $this->getCurrentHousehold($client->getId());
        $where = $this->_db->quoteInto('household_id = ?', $newHouseId);
        $change = array('spouse_id' => $newSpouseId);
        $this->_db->update('household', $change, $where);
    }
    
    private function insertNeeds($needs, $caseId){
        $newNeeds = array();
        foreach($needs as $need){
            $needData = $this->disassembleCaseNeedModel($need);
            $needData['case_id'] = $caseId;
            $this->_db->insert('case_need', $needData);
            $need->setCaseNeedId($this->_db->lastInsertId('case_need'));
            $newNeeds[] = $need;
        }
        return $newNeeds;
    }
    
    private function insertVisits($visits, $caseId){
        $newVisits = array();
        foreach($visits as $visit){
            $visitData = $this->disassembleCaseVisitModel($visit);
            $visitData['case_id'] = $caseId;
            $this->_db->insert('case_visit', $visitData);
            
            //Insert individual visitors in case_visitors table
            $newVisitId = $this->_db->lastInsertId('case_visit');
            $this->insertVisitors($visit->getVisitors(), $newVisitId);
            
            $visit->setVisitId($newVisitId);
            $newVisits[] = $visit;
        }
        return $newVisits;
    }
    
    private function insertVisitors($visitors, $visitId){
        foreach($visitors as $visitor){
            $visitorData = array(
                'visit_id' => $visitId,
                'user_id' => $visitor->getUserId(),
            );
            $this->_db->insert('case_visitors', $visitorData);
        }
    }
}
