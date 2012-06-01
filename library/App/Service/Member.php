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
                array('u' => 'user'),
                'c.created_user_id = u.user_id',
                array('user_first_name' => 'u.first_name', 'user_last_name' => 'u.last_name')
            )
            ->join(
                array('h' => 'household'),
                'c.client_id = h.mainclient_id OR c.client_id = h.spouse_id',
                array('h.household_id')
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
                array(
                    'do_not_help_user_id' => 'd.create_user_id',
                    'do_not_help_date' => 'd.added_date',
                    'do_not_help_reason' => 'd.reason',
                )
            )
            ->where('h.current_flag = 1')
            ->where('c.client_id = ?', $clientId);

        $results = $this->_db->fetchRow($select);
        return $this->buildClientModel($results);
    }

    // Given a case ID, return a Case object populated with all relevant data.
    public function getCaseById($caseId)
    {
        $select = $this->_db->select()
            ->from(array('s' => 'client_case'), array('s.case_id', 's.opened_date', 's.status'))
            ->join(
                array('u' => 'user'),
                's.opened_user_id = u.user_id',
                array(
                    'u.user_id',
                    'user_first_name' => 'u.first_name',
                    'user_last_name' => 'u.last_name',
                )
            )
            ->join(
                array('h' => 'household'),
                's.household_id = h.household_id',
                array('client_id' => 'h.mainclient_id')
            )
            ->where('s.case_id = ?', $caseId);

        $results = $this->_db->fetchRow($select);
        return $this->buildCaseModel($results);
    }

    public function getCommentsByClientId($clientId)
    {
        $select = $this->_db->select()
            ->from(array('c' => 'client_comment'), array(
                'comment_id' => 'c.clientcomment_id',
                'comment_date' => 'c.comment_date',
                'c.comment',
            ))
            ->join(
                array('u' => 'user'),
                'c.user_id = u.user_id',
                array('u.user_id', 'u.first_name', 'u.last_name')
            )
            ->where('c.client_id = ?', $clientId)
            ->order('comment_date DESC', 'comment_id');
        $results = $this->_db->fetchAssoc($select);
        return $this->buildCommentModels($results);
    }

    public function getCommentsByCaseId($caseId)
    {
        $select = $this->_db->select()
            ->from(array('c' => 'case_comment'), array(
                'comment_id' => 'c.casecomment_id',
                'comment_date' => 'c.case_date',
                'c.comment',
            ))
            ->join(
                array('u' => 'user'),
                'c.user_id = u.user_id',
                array('u.user_id', 'u.first_name', 'u.last_name')
            )
            ->where('c.case_id = ?', $caseId)
            ->order('comment_date DESC', 'comment_id');
        $results = $this->_db->fetchAssoc($select);
        return $this->buildCommentModels($results);
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
    public function getCasesByClientId($clientId){
        $select  = $this->_db->select()
            ->from(array('s' => 'client_case'), array('s.case_id', 's.opened_date', 's.status'))
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
                'h.mainclient_id = c.client_id OR h.spouse_id = c.client_id',
                array(
                    'c.client_id',
                    'c.first_name',
                    'c.last_name',
                    'c.cell_phone',
                    'c.home_phone',
                    'c.work_phone',
                )
            )
            ->where('c.client_id = ?', $clientId)
            ->order('s.opened_date DESC', 's.case_id');
		$results = $this->_db->fetchAll($select);
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
            ->order(array('u.first_name', 'u.last_name', 'u.user_id'));

        $results = $this->_db->fetchAssoc($select);
        return $this->buildUserModels($results);
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

    //Fetches an array of populated CaseNeed objects relevant to the given case
    public function getNeedsByCase($caseId){
        $needs = array();
        $select = $this->_db->select()
                    ->from(array('cn' => 'case_need'),
                           array('caseNeedId' => 'cn.caseneed_id',
                           'cn.need',
                           'cn.amount'))
                    ->joinLeft(array('cr' => 'check_request'),
                               'cn.caseneed_id = cr.caseneed_id',
                               array('cr.checkrequest_id', 'cr.request_date', 'cr.issue_date'))
                    ->joinLeft(array('r' => 'referral'),
                               'cn.caseneed_id = r.caseneed_id',
                               array('r.referred_date', 'r.reason', 'r.referred_to'))
                    ->where('cn.case_id = ?', $caseId);
        $results = $this->_db->fetchAll($select);

        foreach($results as $row){
            $need = new Application_Model_Impl_CaseNeed();
            $need->setId($row['caseNeedId']);
            $need->setNeed($row['need']);
            $need->setAmount($row['amount']);

            if ($row['referred_date']) {
                $referral = new Application_Model_Impl_Referral();
                $referral
                    ->setDate($row['referred_date'])
                    ->setReason($row['reason'])
                    ->setReferredTo($row['referred_to']);
                $need->setReferralOrCheckReq($referral);
            } else if ($row['checkrequest_id'] !== null) {
                $checkReq = new Application_Model_Impl_CheckReq();
                $checkReq
                    ->setId($row['checkrequest_id'])
                    ->setRequestDate($row['request_date'])
                    ->setIssueDate($row['issue_date']);
                $need->setReferralOrCheckReq($checkReq);
            }

            $needs[$row['caseNeedId']] = $need;
        }
        return $needs;
    }

    //Fetches an array of populated CaseVisit objects relevant to the given case
    public function getVisitsByCase($caseId){
        $select = $this->_db->select()
            ->from(
                array('cv' => 'case_visit'),
                array('cv.visit_id', 'cv.visit_date', 'cv.miles', 'cv.hours')
            )
            ->join(
                array('v' => 'case_visitors'),
                'cv.visit_id = v.visit_id',
                array('v.user_id')
            )
            ->where('cv.case_id = ?', $caseId);
        $results = $this->_db->fetchAll($select);

        $visits = array();

        foreach ($results as $result) {
            if (isset($visits[$result['visit_id']])) {
                $visit = &$visits[$result['visit_id']];
            } else {
                $visits[$result['visit_id']] = $visit = new Application_Model_Impl_CaseVisit();
                $visit
                    ->setId($result['visit_id'])
                    ->setDate($result['visit_date'])
                    ->setMiles($result['miles'])
                    ->setHours($result['hours'])
                    ->setVisitors(array());
            }

            $user = new Application_Model_Impl_User();
            $user->setUserId($result['user_id']);
            $visit->addVisitor($user);

            unset($visit);
        }

        return $visits;
    }
    
    //Gets all members of past & current households of client
    //Returns each list of household members as an array of Householder objects with the household address object as the first element.
    //Each list is an element in a two dimensional associative array (ie. [household_id][array of members])
    public function getClientHouseholdHistory($clientId){
        //Get list of all past & current client households
        $select = $this->_db->select()
                ->from(array('h' => 'household'), 'household_id')
                ->joinLeft(array('a' => 'address'),
                            'h.address_id = a.address_id')
                ->where('mainclient_id = ?', $clientId)
                ->orWhere('spouse_id = ?', $clientId);
        $results = $this->_db->fetchAll($select);
        $arr = array();
        $temp = array();
        
        //Get all the members in each household
        foreach($results as $row){
            $arr[$row['household_id']] = $this->getHouseholdersByHouseClientIds($row['household_id'], $clientId);
            array_unshift($arr[$row['household_id']], $this->buildAddrModel($row));
        }
        return $arr;
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
                    'create_user_id' => $client->getUser()->getUserId(),
                    'added_date' => $client->getCreatedDate(),
                    'reason' => $client->getDoNotHelp()->getReason(),
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

            $this->changeHouseholders($householdId, $householders);

            $this->changeEmployers($client->getId(), $employers);

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
            $caseData['household_id'] = $case->getClient()->getHouseholdId();
            $this->_db->insert('client_case', $caseData);
            $case->setId($this->_db->lastInsertId());

            foreach ($case->getNeeds() as $need) {
                $this->changeCaseNeed($case->getId(), $need);
            }

            $this->_db->commit();
            return $case;
        }catch(Exception $ex){
            $this->_db->rollBack();
            throw $ex;
        }
    }

    public function createReferral($needId, $referral)
    {
        $this->_db->insert('referral', array(
            'caseneed_id' => $needId,
            'referred_date' => $referral->getDate(),
            'reason' => $referral->getReason(),
            'referred_to' => $referral->getReferredTo(),
        ));
        $referral->setId($this->_db->lastInsertId());
        return $referral;
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

    public function createClientComment($clientId, Application_Model_Impl_Comment $comment)
    {
        $this->_db->insert('client_comment', array(
            'client_id' => $clientId,
            'user_id' => $comment->getUser()->getUserId(),
            'comment_date' => $comment->getDateTime(),
            'comment' => $comment->getText(),
        ));
        $comment->setId($this->_db->lastInsertId());
        return $comment;
    }

    public function createCaseComment($caseId, Application_Model_Impl_Comment $comment)
    {
        $this->_db->insert('case_comment', array(
            'case_id' => $caseId,
            'user_id' => $comment->getUser()->getUserId(),
            'case_date' => $comment->getDateTime(),
            'comment' => $comment->getText(),
        ));
        $comment->setId($this->_db->lastInsertId());
        return $comment;
    }

    /****** PUBLIC EDIT/UPDATE/DELETE QUERIES  ******/

    public function editClient($client, $changedHouseholders, $changedEmployers,
        $removedHouseholders, $removedEmployers, $move, $maritalStatusChange)
    {
        $this->_db->beginTransaction();

        try {
            // Update client.
            $this->_db->update(
                'client',
                $this->disassembleClientModel($client),
                $this->_db->quoteInto('client_id = ?', $client->getId())
            );

            // Remove/update do not help entry.
            $this->_db->delete(
                'do_not_help',
                $this->_db->quoteInto('client_id = ?', $client->getId())
            );

            if ($client->isDoNotHelp()) {
                // If the client is marked do-not-help, insert or update do-not-help record.
                $doNotHelp = $client->getDoNotHelp();

                $this->_db->insert('do_not_help', array(
                    'client_id' => $client->getId(),
                    'create_user_id' => $doNotHelp->getUser()->getUserId(),
                    'added_date' => $doNotHelp->getDateAdded(),
                    'reason' => $doNotHelp->getReason(),
                ));
            }

            // Insert/update employers.
            $this->changeEmployers($client->getId(), $changedEmployers);

            // Remove employers.
            $this->removeEmployers($removedEmployers);

            // Insert/update address.
            $addrFields = $this->disassembleAddrModel($client->getCurrentAddr());
            $addrFields['client_id'] = $client->getId();

            if ($move) {
                // If the client moved, insert a new address.
                $this->_db->insert('address', $addrFields);

                $client->getCurrentAddr()->setId($this->_db->lastInsertId());
            } else {
                // If the client didn't move, update the existing address.
                $this->_db->update(
                    'address',
                    $addrFields,
                    $this->_db->quoteInto('address_id = ?', $client->getCurrentAddr()->getId())
                );
            }

            // Insert/update spouse.
            $oldSpouse = null;

            if ($maritalStatusChange) {
                if ($client->getMaritalStatus() === 'Married') {
                    // If the client got married, insert the new spouse.
                    $this->_db->insert(
                        'client',
                        $this->disassembleSpouseModel($client->getSpouse())
                    );

                    $client->getSpouse()->setId($this->_db->lastInsertId());
                } else {
                    // If the client got unmarried, update the old spouse and create a new address.
                    $oldSpouse = $client->getSpouse();
                    $oldSpouse
                        ->setMaritalStatus($client->getMaritalStatus())
                        ->setCurrentAddr(clone $client->getCurrentAddr());

                    $this->_db->update(
                        'client',
                        array('marriage_status' => $oldSpouse->getMaritalStatus()),
                        $this->_db->quoteInto('client_id = ?', $oldSpouse->getId())
                    );

                    $addrFields = $this->disassembleAddrModel($oldSpouse->getCurrentAddr());
                    $addrFields['client_id'] = $oldSpouse->getId();

                    $this->_db->insert('address', $addrFields);

                    $oldSpouse->getCurrentAddr()->setId($this->_db->lastInsertId());
                    $client->setSpouse(null);
                }
            } else {
                if ($client->isMarried()) {
                    // If the client was married and stayed married, update the spouse.
                    $this->_db->update(
                        'client',
                        $this->disassembleSpouseModel($client->getSpouse()),
                        $this->_db->quoteInto('client_id = ?', $client->getSpouse()->getId())
                    );
                }
            }

            // Update/insert household.
            if ($move || $maritalStatusChange) {
                // If the client moved, got married, and/or got unmarried, mark the old household as
                // not current and insert a new household.
                $this->_db->update(
                    'household',
                    array('current_flag' => 0),
                    $this->_db->quoteInto('household_id = ?', $client->getHouseholdId())
                );

                $this->_db->insert('household', array(
                    'address_id' => $client->getCurrentAddr()->getId(),
                    'mainclient_id' => $client->getId(),
                    'spouse_id' => $client->isMarried() ? $client->getSpouse()->getId() : null,
                    'current_flag' => 1,
                ));

                $client->setHouseholdId($this->_db->lastInsertId());
                $householdIds = array($client->getHouseholdId());

                // If the client got unmarried, insert a new household for the old spouse.
                if ($oldSpouse) {
                    $this->_db->insert('household', array(
                        'address_id' => $oldSpouse->getCurrentAddr()->getId(),
                        'mainclient_id' => $oldSpouse->getId(),
                        'current_flag' => 1,
                    ));

                    $oldSpouse->setHouseholdId($this->_db->lastInsertId());
                    $householdIds[] = $oldSpouse->getHouseholdId();
                }

                // (Re-)insert household members for the client and the old spouse (if present).
                foreach ($householdIds as $householdId) {
                    foreach ($changedHouseholders as $changedHouseholder) {
                        $changedHouseholder->setId(null);
                    }

                    $this->changeHouseholders($householdId, $changedHouseholders);
                }
            } else {
                // Insert/update household members.
                $this->changeHouseholders($client->getHouseholdId(), $changedHouseholders);

                // Remove household members.
                $this->removeHouseholders($removedHouseholders);
            }

            $this->_db->commit();
        } catch (Exception $ex) {
            $this->_db->rollBack();
            throw $ex;
        }

        return $client;
    }

    // Closes the case with the specified ID.
    public function closeCaseById($caseId)
    {
        $this->_db->update(
            'client_case',
            array('status' => 'Closed'),
            $this->_db->quoteInto('case_id = ?', $caseId)
        );
    }

    public function changeCaseNeed($caseId, $need)
    {
        $needFields = $this->disassembleCaseNeedModel($need) + array('case_id' => $caseId);

        if ($need->getId() === null) {
            // Insert new case need.
            $this->_db->insert('case_need', $needFields);
            $need->setId($this->_db->lastInsertId());
        } else {
            $this->_db->update(
                'case_need',
                $needFields,
                $this->_db->quoteInto('caseneed_id = ?', $need->getId())
            );
        }

        return $need;
    }

    public function removeCaseNeeds($needs)
    {
        if (!$needs) {
            return;
        }

        $needIds = array();
        foreach ($needs as $need) {
            $needIds[] = $need->getId();
        }

        $this->_db->delete('case_need', $this->_db->quoteInto('caseneed_id IN (?)', $needIds));
    }

    public function changeCaseVisit($caseId, $visit)
    {
        $this->_db->beginTransaction();

        try {
            $visitFields = $this->disassembleCaseVisitModel($visit) + array('case_id' => $caseId);

            if ($visit->getId() === null) {
                // Insert new case visit.
                $this->_db->insert('case_visit', $visitFields);
                $visit->setId($this->_db->lastInsertId());
            } else {
                // Update case visit, temporarily removing old case visitors.
                $where = $this->_db->quoteInto('visit_id = ?', $visit->getId());

                $this->_db->delete('case_visitors', $where);
                $this->_db->update('case_visit', $visitFields, $where);
            }

            // (Re)add case visitors.
            foreach ($visit->getVisitors() as $visitor) {
                $this->_db->insert('case_visitors', array(
                    'visit_id' => $visit->getId(),
                    'user_id' => $visitor->getUserId(),
                ));
            }

            $this->_db->commit();
        } catch (Exception $ex) {
            $this->_db->rollBack();
            throw $ex;
        }

        return $visit;
    }

    public function removeCaseVisits($visits)
    {
        if (!$visits) {
            return;
        }

        $visitIds = array();
        foreach ($visits as $visit) {
            $visitIds[] = $visit->getId();
        }

        $this->_db->beginTransaction();

        try {
            // Remove case visitors and then case visit.
            $where = $this->_db->quoteInto('visit_id IN (?)', $visitIds);

            $this->_db->delete('case_visitors', $where);
            $this->_db->delete('case_visit', $where);

            $this->_db->commit();
        } catch (Exception $ex) {
            $this->_db->rollback();
            throw $ex;
        }
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
    
    //Returns the marriage status of the given client
    private function getClientMarriageStatus($clientId){
        $select = $this->_db->select()
                ->from('client', 'marriage_status')
                ->where('client_id = ?', $clientId);
        $results = $this->_db->fetchRow($select);
        return $results['marriage_status'];
    }
    
    private function getHouseholdersByHouseClientIds($houseId, $clientId){
        $hMembers = array();
        
        //Get spouse if exists, need to grab both id's
        $select = $this->_db->select()
                ->from('household', array('mainclient_id', 'spouse_id'))
                ->where('household_id = ?', $houseId);
        $results = $this->_db->fetchRow($select);
        
        //If the clientId matches the spouse_id then given client was added as a spouse
        //need to indicated thier spouse as the main client
        $spouseId = ($results['spouse_id'] === $clientId) ?
            $results['mainclient_id'] : $results['spouse_id'];
        if($spouseId){
            $householder = new Application_Model_Impl_Householder();
            $select = $this->_db->select()
                    ->from(array('c' => 'client'),
                           array('client_id',
                                 'first_name',
                                 'last_name',
                                 'birthdate'))
                    ->where('c.client_id = ?', $spouseId);
            $results = $this->_db->fetchRow($select);
            $householder
                ->setId($results['client_id'])
                ->setFirstName($results['first_name'])
                ->setLastName($results['last_name'])
                ->setRelationship('Spouse')
                ->setBirthDate($results['birthdate']);
            $hMembers[] = $householder;
        }
        
        //Get householders except spouse
        $select = $this->_db->select()
                ->from(array('hm' => 'hmember'))
                ->join(array('h' => 'household'),
                       'h.household_id = hm.household_id')
                ->where('h.household_id = ?', $houseId);
        $results = $this->_db->fetchAll($select);
        foreach($results as $row)
            $hMembers[] = $this->buildHouseholderModel($row);
        return $hMembers;
   }
   
    /****** PRIVATE CREATE/INSERT QUERIES  ******/

    private function changeHouseholders($householdId, $householders)
    {
        foreach ($householders as $householder) {
            $householderFields = $this->disassembleHouseholderModel($householder);
            $householderFields['household_id'] = $householdId;

            if ($householder->getId() === null) {
                $this->_db->insert('hmember', $householderFields);

                $householder->setId($this->_db->lastInsertId());
            } else {
                $this->_db->update(
                    'hmember',
                    $householderFields,
                    $this->_db->quoteInto('hmember_id = ?', $householder->getId())
                );
            }
        }
    }

    private function removeHouseholders($householders)
    {
        if (!$householders) {
            return;
        }

        $householderIds = array();
        foreach ($householders as $householder) {
            $householderIds[] = $householder->getId();
        }

        $this->_db->delete(
            'hmember',
            $this->_db->quoteInto('hmember_id IN (?)', $householderIds)
        );
    }

    private function changeEmployers($clientId, $employers)
    {
        foreach ($employers as $employer) {
            $employerFields = $this->disassembleEmployerModel($employer);
            $employerFields['client_id'] = $clientId;

            if ($employer->getId() === null) {
                $this->_db->insert('employment', $employerFields);

                $employer->setId($this->_db->lastInsertId());
            } else {
                $this->_db->update(
                    'employment',
                    $employerFields,
                    $this->_db->quoteInto('employment_id = ?', $employer->getId())
                );
            }
        }
    }

    private function removeEmployers($employers)
    {
        if (!$employers) {
            return;
        }

        $employerIds = array();
        foreach ($employers as $employer) {
            $employerIds[] = $employer->getId();
        }

        $this->_db->delete(
            'employment',
            $this->_db->quoteInto('employment_id IN (?)', $employerIds)
        );
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

        $user = new Application_Model_Impl_User();
        $user
            ->setUserId($dbResult['created_user_id'])
            ->setFirstName($dbResult['user_first_name'])
            ->setLastName($dbResult['user_last_name']);

        if ($dbResult['do_not_help_reason'] !== null) {
            $doNotHelpUser = new Application_Model_Impl_User();
            $doNotHelpUser->setUserId($dbResult['do_not_help_user_id']);

            $doNotHelp = new Application_Model_Impl_DoNotHelp();
            $doNotHelp
                ->setUser($doNotHelpUser)
                ->setDateAdded($dbResult['do_not_help_date'])
                ->setReason($dbResult['do_not_help_reason']);
        } else {
            $doNotHelp = null;
        }

        $client = new Application_Model_Impl_Client();
        $client
            ->setId($dbResult['client_id'])
            ->setUser($user)
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
            ->setHouseholdId($dbResult['household_id'])
            ->setCurrentAddr($addr)
            ->setDoNotHelp($doNotHelp);

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

    private function buildCaseModels($results)
    {
        $cases = array();
        foreach ($results as $id => $result) {
            $cases[$id] = $this->buildCaseModel($result);
        }
        return $cases;
    }

    private function buildCaseModel($result){
        $user = new Application_Model_Impl_User();
        $user
            ->setUserId($result['user_id'])
            ->setFirstName($result['user_first_name'])
            ->setLastName($result['user_last_name']);
        $case = new Application_Model_Impl_Case();
        $case
            ->setId($result['case_id'])
            ->setOpenedDate($result['opened_date'])
            ->setStatus($result['status'])
            ->setOpenedUser($user)
            ->setClient($this->getClientById($result['client_id']))
            ->setVisits($this->getVisitsByCase($result['case_id']))
            ->setNeeds($this->getNeedsByCase($result['case_id']));
        return $case;
    }

    private function buildCommentModels($dbResults)
    {
        $comments = array();

        foreach ($dbResults as $dbResult) {
            $user = new Application_Model_Impl_User();
            $user
                ->setUserId($dbResult['user_id'])
                ->setFirstName($dbResult['first_name'])
                ->setLastName($dbResult['last_name']);

            $comment = new Application_Model_Impl_Comment();
            $comment
                ->setId($dbResult['comment_id'])
                ->setUser($user)
                ->setDateTime($dbResult['comment_date'])
                ->setText($dbResult['comment']);

            $comments[$dbResult['comment_id']] = $comment;
        }

        return $comments;
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
    
    //Builds a singe householder model object
    private function buildHouseholderModel($results){
        $householder = new Application_Model_Impl_Householder();
        $householder
            ->setId($results['hmember_id'])
            ->setFirstName($results['first_name'])
            ->setLastName($results['last_name'])
            ->setRelationship($results['relationship'])
            ->setBirthDate($results['birthdate'])
            ->setDepartDate($results['left_date']);
        return $householder;
    }
    
    private function buildAddrModel($results){
        $addr = new Application_Model_Impl_Addr();
        $addr
            ->setId($results['address_id'])
            ->setStreet($results['street'])
            ->setCity($results['city'])
            ->setState($results['state'])
            ->setZip($results['zipcode']);
        return $addr;
    }

    /****** IMPL OBJECT DISASSEMBLERS  ******/

    private function disassembleClientModel($client)
    {
        $options = array(
            'first_name' => $client->getFirstName(),
            'last_name' => $client->getLastName(),
            'other_name' => $client->getOtherName(),
            'marriage_status' => $client->getMaritalStatus(),
            'birthdate' => $client->getBirthDate(),
            'ssn4' => $client->getSsn4(),
            'cell_phone' => $client->getCellPhone(),
            'home_phone' => $client->getHomePhone(),
            'work_phone' => $client->getWorkPhone(),
            'member_parish' => $client->getParish(),
            'veteran_flag' => (int)$client->isVeteran(),
        );
        if ($client->getUser() !== null) {
            $options['created_user_id'] = $client->getUser()->getUserId();
        }
        if ($client->getCreatedDate() !== null) {
            $options['created_date'] = $client->getCreatedDate();
        }
        return $options;
    }

    private function disassembleSpouseModel($client)
    {
        $options = $this->disassembleClientModel($client);
        unset($options['other_name']);
        unset($options['cell_phone']);
        unset($options['work_phone']);
        unset($options['veteran_flag']);
        return $options;
    }

    private function disassembleCaseModel($case){
        return array(
            'opened_user_id' => $case->getOpenedUser()->getUserId(),
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

    private function disassembleHouseholderModel($householder)
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
            'visit_date' => $visit->getDate(),
            'miles' => $visit->getMiles(),
            'hours' => $visit->getHours(),
        );
    }

    private function disassembleCheckRequestModel($request){
        return array(
            'caseneed_id' => $request->getCaseNeedId(),
            'user_id' => $request->getUser()->getUserId(),
            'request_date' => $request->getRequestDate(),
            'amount' => $request->getAmount(),
            'comment' => $request->getComment(),
            'signee_userid' => ($request->getSigneeUser() !== null)
                ? $request->getSigneeUser()->getUserId() : null,
            'check_number' => $request->getCheckNumber(),
            'issue_date' => $request->getIssueDate(),
            'status' => $request->getStatus(),
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
