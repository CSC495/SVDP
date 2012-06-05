<?php
/**
 *Servive file providing the treasurer controller database access.
 */
class App_Service_TreasurerService {
    /**
     *Database adapter for service methods.
     *
     * @var Zend_Db_Adapter_Abstract
    */
    private $_db;
    
    /**
     *Creates a connection to the DB available to the class.
     *
     *@return void
    */
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /******* PUBLIC GET QUERIES *******/
    
    /**
     *Gets information of indicated check request.
     *
     *@param int id of indicated check requets
     *@return Application_Model_Impl_CheckReq
    */
    public function getCheckReqById($id)
    {
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
    
    /**
     *Closes the indicated check request and subtracts the amount from the parish's available funds.
     *
     *@param string id of signee user
     *@param int id of indicated check request
     *@param int check number issued for the indicated check request
     *@return void
    */
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
    
    /**
     *Updates the indicated check request with data in the given CheckReq object.
     *
     *@param Application_Model_Impl_CheckReq
     *@return void
    */
    public function updateCheckRequest($reqObj)
    {
	$reqData = $this->disassembleCheckRequestModel($reqObj);
	$where = $this->_db->quoteInto('checkrequest_id = ?', $reqObj->getId());
	$this->_db->update('check_request', $reqData, $where);
    }
    
    /**
     *Updates parish funds to given amount.
     *
     *@param int new amount of parish funds
     *@return void
    */
    public function updateParishFunds($amount)
    {
	$change = array('available_funds' => $amount);
	$this->_db->update('parish_funds', $change, '1');
    }
    
    /**
     *Updates the indicated comment associated with the given check request id.
     *
     *@param Application_Model_Impl_Comment
     *@param int id of check request associated with comment
     *@return void
    */
    public function updateCheckReqComment($comment, $id)
    {
	$change = array('comment' => $comment);
	$where = $this->_db->quoteInto('checkrequest_id = ?', $id);
	$this->_db->update('check_request', $change, $where);
    }
	
    /**
     *Denies the indicated check request.
     *
     *@param id of the check request to deny
     *@return void
    */
    public function denyCheckRequest($id, $userId){
	$where = $this->_db->quoteInto('checkrequest_id = ?', $id);
	$change = array('status' => 'D',
			'signee_userid' => $userId);
	$this->_db->update('check_request', $change, $where);
    }
    
    /****** PRIVATE GET QUERIES  ******/
    
    /**
     *Returns the amount of the indicated chech request.
     *
     *@param int indicated check request id
     *@return void
    */
    private function getCheckAmount($id)
    {
	$select = $this->_db->select()
		->from('check_request', 'amount')
		->where('checkrequest_id = ?', $id);
	$results = $this->_db->fetchRow($select);
	return $results['amount'];
    }
    
    /**
     *Gets the parish's currently available funds.
     *
     *@return int available funds
    */
    private function getParishFunds()
    {
	$select = $this->_db->select()
		->from('parish_funds', 'available_funds');
	$results = $this->_db->fetchRow($select);
	return $results['available_funds'];
    }
    
    /****** IMPL OBJECT BUILDERS  ******/
    
    /**
     *Builds a CheckReq object.
     *
     *Creates a CheckReq object, populates it with the data in the given associative array
     *
     *@param mixed[]
     *@return Application_Model_Impl_CheckReq
    */
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
            ->setUserId($results['user_id'])
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
    
    /**
     *Extracts properties of a CheckReq object.
     *
     *@param Application_Model_Impl_CheckReq
     *@return mixed[string]
    */
    private function disassembleCheckRequestModel($request){
        return array(
            'caseneed_id' => $request->getCaseNeedId(),
            'user_id' => $request->getUserId(),
            'request_date' => $request->getRequestDate(),
            'amount' => $request->getAmount(),
            'comment' => $request->getComment(),
            'signee_userid' => ($request->getSigneeUserId() !== null)
                ? $request->getSigneeUserId() : null,
            'check_number' => App_Formatting::emptyToNull($request->getCheckNumber()),
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
