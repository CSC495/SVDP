<?php
/**
 * Model class holds information relavent to a non-specfic report
 *WILL BE REPLACED WITH A CLASS BETTER SUITED FOR THE SPECIFIC REPORT
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$case->setId(...)->setRequestDate(...)` and so on.
 */
class Application_Model_Impl_GenReport{
    private $_caseId = null;
    private $_numRefs = null;
    private $_numHMembers = null;
    
    public function getCaseId(){
        return $this->_caseId;
    }
    
    public function setCaseId($id){
        $this->_caseId = $id;
        return $this;
    }
    
    public function getNumRefs(){
        return $this->_numRefs;
    }
    
    public function setNumRefs($num){
        $this->_numRefs = $num;
        return $this;
    }
    
    public function getNumHMembers(){
        return $this->_numHMembers;
    }
    
    public function setNumHMembers($num){
        $this->_numHMembers = $num;
        return $this;
    }
}