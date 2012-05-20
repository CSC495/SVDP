<?php

class App_Service_DocumentService {
    private $_db;
    
    function __construct(){
        $this->_db = Zend_Db_Table::getDefaultAdapter();
    }
    
    /******* PUBLIC GET QUERIES *******/
    
    /***
     * Get list of all the documents
     */
    public function getDocuments()
    {
        $select = $this->_db->select()->from('documents');
        
        $results = $this->_db->fetchAll($select);
        
        return( $this->buildDocuments($results) );
    }
    
    /***
     * Gets information about a single document
     */
    public function getDocument($id)
    {
        $select = $this->_db->select()->from('documents')->where('doc_id = ?',$id);
        
        $result = $this->_db->fetchRow($select);
        
        return( $this->buildDocument($result) );
    }
    
    //Given a timespan bouned by the given start and end date
    //Gets the total miles of each case's visits within the timespan
    //Returns an associative array with the case_id as key and total miles as value
    //DOES NOT DISCRIMINATE BETWEEN OPEN AND CLOSED CASES
    public function getCaseVisitMiles($startDate, $endDate){
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalMiles' => new Zend_Db_Expr('SUM(cv.miles)')))
                ->joinLeft(array('cv' => 'case_visit'), 'cc.case_id = cv.case_id')
                ->where('cv.visit_date >= ?', $startDate)
                ->where('cv.visit_date <= ?', $endDate)
                ->group('cc.case_id');
        $results = $this->_db->fetchAll($select);
        $arr = $this->getAssocOfCases();
        foreach($results as $row)
            $arr[$row['id']] = $row['totalMiles'];
        return $arr;
    }
    
    //Given a timespan bouned by the given start and end date
    //Gets the total hours of each case's visits within the timespan
    //Returns an associative array with the case_id as key and total hours as value
    //DOES NOT DISCRIMINATE BETWEEN OPEN AND CLOSED CASES
    public function getCaseVisitHours($startDate, $endDate){
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalHours' => new Zend_Db_Expr('SUM(cv.hours)')))
                ->joinLeft(array('cv' => 'case_visit'), 'cc.case_id = cv.case_id')
                ->where('cv.visit_date >= ?', $startDate)
                ->where('cv.visit_date <= ?', $endDate)
                ->group('cc.case_id');
        $results = $this->_db->fetchAll($select);
        $arr = $this->getAssocOfCases();
        foreach($results as $row)
            $arr[$row['id']] = $row['totalHours'];
        return $arr;
    }
    
    //Gets all closed check requests (have an issue date)
    //Returns an array of populated CheckReq objects
    public function getClosedCheckReqs(){
        $select = $this->_db->select()
                ->from('check_request')
                ->where('issue_date IS NOT NULL');
        $results = $this->_db->fetchAll($select);
        $closedReqs = array();
        foreach($results as $row)
            $closedReqs[] = $this->buildCheckRequestModel($row);
        return $closedReqs;
    }
    
    //Gets the number of references and number of hmembers per case
    //Returns an array of populated GenReport objects
    //THIS WILL BE RENAMED TO REFLECT THE SPECIFIC REPORT THAT USES IT
    public function getGenReports(){
        return $this->getNumRefs($this->getNumMems());
    }
    
    /****** PUBLIC EDIT/UPDATE/DELETE QUERIES  ******/
    
    // temp
    public function deleteDocument($doc)
    {
        $result = $this->_db->delete('documents','doc_id =' . $doc->getId());
        return $result;
    }
    
    /***
     * Updates information about a particular document
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
     * Creates a new document
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
    
    //Returns an associative array with every case_id as key and 0 as value
    private function getAssocOfCases(){
        $select = $this->_db->select()
                ->from('client_case', array('id' => 'case_id'));
        $results = $this->_db->fetchAll($select);
        $ids = array();
        foreach($results as $row)
            $ids[$row['id']] = '0';
        return $ids;
    }
    
    //Gets the total number of household members associated with each case
    //Returns an array of GenReport objects with _caseId & _numHMembers populated
    private function getNumMems(){
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalMems' => new Zend_Db_Expr('COUNT(hmem.hmember_id)')))
                ->joinLeft(array('hmem' => 'hmember'),
                           'cc.household_id = hmem.household_id')
                ->group('cc.case_id');
        $results = $this->_db->fetchAll($select);
        $arr = array();
        foreach($results as $row){
            $report = new Application_Model_Impl_GenReport();
            $report->setCaseId($row['id']);
            $report->setNumHMembers($row['totalMems']);
            $arr[$row['id']] = $report;
        }
        return $arr;
    }
    
    //Given an array of GenReport objects with _caseId & _numHMembers populated
    //Gets the total number of referrals associated with each case
    //Returns the given array with all object's _numRefs populated
    private function getNumRefs($arr){
        $select = $this->_db->select()
                ->from(array('cc' => 'client_case'),
                       array('id' => 'cc.case_id',
                             'totalRefs' => new Zend_Db_Expr('COUNT(r.referral_id)')))
                ->joinLeft(array('cn' => 'case_need'),
                           'cc.case_id = cn.case_id')
                ->joinLeft(array('r' => 'referral'),
                           'cn.caseneed_id = r.caseneed_id')
                ->group('cc.case_id');
        $results = $this->_db->fetchAll($select);
        foreach($results as $row)
            $arr[$row['id']]->setNumRefs($row['totalRefs']);
        return $arr;
    }
    
    /****** IMPL OBJECT BUILDERS  ******/
    
    /***
     * Builds the list of docuemnts from a row set
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
    
    /***
     * Builds a single document
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