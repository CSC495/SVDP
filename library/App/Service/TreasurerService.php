<?php

class App_Service_TreasurerService {
    private $_db;
    
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /******* PUBLIC GET QUERIES *******/
    
    //Given a checkrequest_id returns a populated CheckReq object
    public function getCheckReqById($id){
        $select = $this->_db->select()
                ->from(array('cr' => 'check_request'))
		->join(array('cn' => 'case_need'),
		       'cn.caseneed_id = cr.caseneed_id',
		       array('caseneedName' => 'cn.need'))
		->join(array('cc' => 'client_case'),
		       'cn.case_id = cc.case_id',
		       array('caseId' => 'cc.case_id'))
		->join(array('u' => 'user'),
		       'u.user_id = cr.user_id',
		       array('userFName' => 'u.first_name',
			     'userLName' => 'u.last_name'))
                ->where('checkrequest_id = ?', $id);
        $results = $this->_db->fetchRow($select);
        return $this->buildCheckRequestModel($results);
    }
    
    /****** PUBLIC EDIT/UPDATE QUERIES  ******/
    
    //Given the treasurer id, check request id, and the check number updates the
    //signee_userid, issue_date of the check_request table
    //Subtracts the check amount from available_funds in parish_funds
    public function closeCheckRequest($userId, $reqId, $checkNum){
	$date = new Zend_Date();
        $curDate = $date->get('YYYY-MM-dd');
	$where = $this->_db->quoteInto('checkrequest_id = ?', $reqId);
	$change = array(
		    'signee_userid' => $userId,
		    'issue_date' => $curDate,
		    'check_number' => $checkNum,
		    'status' => 'I');
	$this->_db->update('check_request', $change, $where);
	
	$change = array('available_funds' => ($this->getParishFunds() - $this->getCheckAmount($reqId)));
	$this->_db->update('parish_funds', $change, '1');
    }
    
    public function updateCheckRequest($reqObj){
	$reqData = $this->disassembleCheckRequestModel($reqObj);
	$where = $this->_db->quoteInto('checkrequest_id = ?', $reqObj->getId());
	$this->_db->update('check_request', $reqData, $where);
    }
    
    //Updates the current available funds to the given amount
    public function updateParishFunds($amount){
	$change = array('available_funds' => $amount);
	$this->_db->update('parish_funds', $change, '1');
    }
    
    public function updateCheckReqComment($comment, $id){
	$change = array('comment' => $comment);
	$where = $this->_db->quoteInto('checkrequest_id = ?', $id);
	$this->_db->update('check_request', $change, $where);
    }
    
    public function denyCheckRequest($id){
	$where = $this->_db->quoteInto('checkrequest_id = ?', $id);
	$change = array('status' => 'D');
	$this->_db->update('check_request', $change, $where);
    }
    
    /****** PRIVATE GET QUERIES  ******/
    
    //Given a checkrequest_id returns the request amount
    private function getCheckAmount($id){
	$select = $this->_db->select()
		->from('check_request', 'amount')
		->where('checkrequest_id = ?', $id);
	$results = $this->_db->fetchRow($select);
	return $results['amount'];
    }
    
    //Returns the current available funds
    private function getParishFunds(){
	$select = $this->_db->select()
		->from('parish_funds', 'available_funds');
	$results = $this->_db->fetchRow($select);
	return $results['available_funds'];
    }
    
    /****** IMPL OBJECT BUILDERS  ******/
    
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
	    ->setCaseNeedName($results['caseneedName'])
            ->setUser($results['user_id'])
	    ->setUserFName($results['userFName'])
	    ->setUserLName($results['userLName'])
            ->setRequestDate($results['request_date'])
	    ->setCase($results['caseId'])
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
            ->setContactLastName($results['contact_lname'])
	    ->setStatus($results['status']);
        return $request;
    }
    
    /****** IMPL OBJECT DISASSEMBLERS ******/
    
    private function disassembleCheckRequestModel($request){
        return array(
            'caseneed_id' => $request->getCaseNeedId(),
            'user_id' => $request->getUserId(),
            'request_date' => $request->getRequestDate(),
            'amount' => $request->getAmount(),
            'comment' => $request->getComment(),
            'signee_userid' => ($request->getSigneeUserId() !== null)
                ? $request->getSigneeUserId() : null,
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
            'contact_lname' => $request->getContactLastName(),
	    'status' => $request->getStatus()
        );
    }
}