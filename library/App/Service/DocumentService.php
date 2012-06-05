<?php
/**
 *Service file providing the document controller database access.
 */
class App_Service_DocumentService {
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
    function __construct()
    {
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /******* PUBLIC GET QUERIES *******/
    
    /**
     * Get list of all the documents.
     * 
     * @return array of Application_Model_Impl_Document
     */
    public function getDocuments()
    {
        $select = $this->_db->select()->from('documents');
        
        $results = $this->_db->fetchAll($select);
        
        return( $this->buildDocuments($results) );
    }
    
    /**
     * Gets information about a single document.
     * 
     * @return Application_Model_Impl_Document
     */
    public function getDocument($id)
    {
        $select = $this->_db->select()->from('documents')->where('doc_id = ?',$id);
        
        $result = $this->_db->fetchRow($select);
        
        return( $this->buildDocument($result) );
    }
    
    /**
     *Gets total miles of case visits within time span.
     *
     *Given a timespan bouned by the given start and end date
     *Gets the total miles of each case's visits within the timespan
     *Returns an associative array with the case_id as key and total miles as value
     *Does not discriminate between open and closed cases
     *
     *@param DateTime $startDate lower bound of time span
     *@param DateTime $endDate upper bound of time span
     *@return associative array key => case_id value => total miles
    */
    public function getCaseVisitMiles($startDate, $endDate)
    {
        $newStartDate = new Zend_Date($startDate, 'MM-dd-YYYY', 'en');
        $newStartDate = $newStartDate->get('YYYY-MM-dd');
        
        $newEndDate = new Zend_Date($endDate, 'MM-dd-YYYY', 'en');
        $newEndDate = $newEndDate->get('YYYY-MM-dd');
        
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalMiles' => 'cv.miles'))
                ->joinLeft(array('cv' => 'case_visit'), 'cc.case_id = cv.case_id')
                ->where('cv.visit_date >= ?', $newStartDate)
                ->where('cv.visit_date <= ?', $newEndDate);
        $results = $this->_db->fetchAll($select);
        $arr = array();
        foreach($results as $row){
            $report = new Application_Model_Impl_GenReport();
            $report->setCaseId($row['id']);
            $report->setTotalMiles($row['totalMiles']);
            $arr[] = $report;
        }
        $arr = $this->getNumMems($arr);
        return $arr;
    }
       
    /**
     *Gets total hours of case visits within time span.
     *
     *Given a timespan bouned by the given start and end date
     *Gets the total hours of each case's visits within the timespan
     *Returns an associative array with the case_id as key and total hours as value
     *Does not discriminate between open and closed cases
     *
     *@param DateTime $startDate lower bound of time span
     *@param DateTime $endDate upper bound of time span
     *@return associative array key => case_id value => total hours
    */
    public function getCaseVisitHours($startDate, $endDate)
    {
        $newStartDate = new Zend_Date($startDate, 'MM-dd-YYYY', 'en');
        $newStartDate = $newStartDate->get('YYYY-MM-dd');
        
        $newEndDate = new Zend_Date($endDate, 'MM-dd-YYYY', 'en');
        $newEndDate = $newEndDate->get('YYYY-MM-dd');
        
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalHours' => new Zend_Db_Expr('SUM(cv.hours)')))
                ->joinLeft(array('cv' => 'case_visit'), 'cc.case_id = cv.case_id')
                ->where('cv.visit_date >= ?', $newStartDate)
                ->where('cv.visit_date <= ?', $newEndDate)
                ->group('cc.case_id');
        $results = $this->_db->fetchAll($select);
        $arr = array();
        foreach($results as $row)
            $arr[$row['id']] = $row['totalHours'];
        return $arr;
    }
    
    /**
     *Gets all closed check requests (have an issue date).
     *
     *@return array of Application_Model_Impl_CheckReq
    */
    public function getClosedCheckReqs()
    {
        $select = $this->_db->select()
                ->from('check_request')
                ->where('issue_date IS NOT NULL');
        $results = $this->_db->fetchAll($select);
        $closedReqs = array();
        foreach($results as $row)
            $closedReqs[] = $this->buildCheckRequestModel($row);
        return $closedReqs;
    }
    
    /**
     *Gets the number of references and number of household members per case withing the given time span.
     *
     *@param DateTime $startDate lower bound of time span
     *@param DateTime $endDate upper bound of time span
     *@return array of Application_Model_Impl_GenReport
     */
    //THIS SHOULD BE RENAMED
    public function getGenReports($startDate, $endDate)
    {
        $newStartDate = new Zend_Date($startDate, 'MM-dd-YYYY', 'en');
        $newStartDate = $newStartDate->get('YYYY-MM-dd');
        
        $newEndDate = new Zend_Date($endDate, 'MM-dd-YYYY', 'en');
        $newEndDate = $newEndDate->get('YYYY-MM-dd');
        return $this->getNumMems($this->getNumRefs($newStartDate, $newEndDate));
    }
    
    /**
     *Gets all check requests of a given case.
     *
     *@param int $caseId
     *@return array of Application_Model_Impl_CheckReq
    */
    public function getCheckReqsByCaseId($caseId)
    {
        $select = $this->_db->select()
                ->from(array('cr' => 'check_request'))
                ->join(array('cn' => 'case_need'), 'cn.caseneed_id = cr.caseneed_id')
                ->join(array('cc' => 'client_case'), 'cn.case_id = cc.case_id', array())
                ->where('cc.case_id = ?', $caseId);
        $results = $this->_db->fetchAll($select);
        $arr = array();
        foreach($results as $row){
            $check = $this->buildCheckRequestModel($row);
            $check->setCaseNeedName($row['need']);
            $arr[] = $check;
        }
        return $arr;
    }
    
    /****** PUBLIC EDIT/UPDATE/DELETE QUERIES  ******/
    
    /**
     *Deletes a document from the database.
     *
     *@param Application_Model_Impl_Document
     *@return int number of rows affected by delete operation
    */
    public function deleteDocument($doc)
    {
        $result = $this->_db->delete('documents','doc_id =' . $doc->getId());
        return $result;
    }
    
    /**
     * Updates information about a particular document.
     * 
     * @param Application_Model_Impl_Document
     * @return void
     */
    public function updateDocument($doc)
    {
        $data = array(  
                        'filename'    => $doc->getName(),
                        'url'         => $doc->getUrl(),
                        'internal_flag'    => $doc->isInternal());
        $where = "doc_id = " . $doc->getId();
        
        $this->_db->update('documents',$data,$where);
    }
    
    /****** PUBLIC CREATE/INSERT QUERIES ******/
    
    /**
     * Creates a new document.
     * 
     * @param Application_Model_Impl_Document
     * @return void
     */
    public function createDocument($doc)
    {
        $data = array(  'doc_id'      => null,
                        'filename'    => $doc->getName(),
                        'url'         => $doc->getUrl(),
                        'internal_flag'    => $doc->isInternal());
        $this->_db->insert('documents',$data);
    }
    
    /****** PRIVATE GET QUERIES  ******/
    
    /**
     *Returns an associative array with every case_id as key and 0 as value.
     *
     *@return associative array key => case_id value => 0
    */
    private function getAssocOfCases()
    {
        $select = $this->_db->select()
                ->from('client_case', array('id' => 'case_id'));
        $results = $this->_db->fetchAll($select);
        $ids = array();
        foreach($results as $row){
            $ids[$row['id']] = '0';
        }
        return $ids;
    }
    
    /**
     *Gets the total number of household members associated with each case.
     *Returns an array of GenReport objects with _caseId & _numHMembers populated
     *
     *@param array of Application_Model_Impl_GenReport
     *@return array of Application_Model_Impl_GenReport
    */
    private function getNumMems($arr)
    {
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalMems' => new Zend_Db_Expr('COUNT(hmem.hmember_id)')))
                ->joinLeft(array('hmem' => 'hmember'),
                           'cc.household_id = hmem.household_id')
                ->group('cc.case_id');
        $results = $this->_db->fetchAll($select);
        $index = 0;
        foreach($results as $row){
            foreach($arr as $rep){
                if($row['id'] == $rep->getCaseId()){
                    $arr[$index]->setNumHMembers($row['totalMems'] + 1);
                }
                $index++;
            }
            $index = 0;
        }
        return $arr;
    }
    
    /**
     *Gets the total number of referrals associated with each case.
     *Given a time span bounded by a start date and an end date (assured to be in international notation)
     *Gets the total number of referrals associated with each case
     *
     *@param Date start date
     *@param Date end date
     *@return array of Application_Model_Impl_GenReport
    */
    private function getNumRefs($newStartDate, $newEndDate)
    {
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalRefs' => new Zend_Db_Expr('COUNT(r.referral_id)')))
                ->joinLeft(array('cn' => 'case_need'),
                           'cc.case_id = cn.case_id')
                ->joinLeft(array('r' => 'referral'),
                           'cn.caseneed_id = r.caseneed_id')
                ->where('r.referred_date >= ?', $newStartDate)
                ->where('r.referred_date <= ?', $newEndDate)
                ->group('cc.case_id');
        $results = $this->_db->fetchAll($select);
        $arr = array();
        foreach($results as $row){
            $report = new Application_Model_Impl_GenReport();
            $report->setCaseId($row['id']);
            $report->setNumRefs($row['totalRefs']);
            $arr[] = $report;
        }
        return $arr;
    }
    
    /****** IMPL OBJECT BUILDERS  ******/
    
    /**
     * Builds the list of docuemnts from a row set.
     * 
     * @param mixed[]
     * @return array of Application_Model_Impl_Document objects
     */
    private function buildDocuments($rowset)
    {
        $list = array();

        foreach($rowset as $row)
        {
            $doc = $this->buildDocument($row);

            array_push($list,$doc);
        }
        return($list);
    }
    
    /**
     * Builds a single document.
     * 
     * @param mixed[]
     * @return Application_Model_Impl_Document
     */
    private function buildDocument($row)
    {
        $doc = new Application_Model_Impl_Document();
        $doc
                ->setId($row['doc_id'])
                ->setUrl($row['url'])
                ->setName($row['filename'])
                ->setInternal($row['internal_flag']);
                
        return($doc);
    }
    
    /**
     *Builds Application_Model_Impl_CheckReq.
     *
     *@param mixed[] data to populate CheckReq object
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
            ->setUserId($results['user_id'])
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
            ->setContactLastName($results['contact_lname'])
            ->setStatus($results['status']);
        return $request;
    }  
}
