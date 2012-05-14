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
                ->from('check_request')
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
		    'check_number' => $checkNum);
	$this->_db->update('check_request', $change, $where);
	
	$change = array('available_funds' => ($this->getParishFunds() - $this->getCheckAmount($reqId)));
	$this->_db->update('parish_funds', $change, '1');
    }
    
    //Updates the current available funds to the given amount
    public function updateParishFunds($amount){
	$change = array('available_funds' => $amount);
	$this->_db->update('parish_funds', $change, '1');
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
}