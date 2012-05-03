<?php

/**
 * Model class representing a single case, which belongs to some client and which is assigned to
 * some parish member.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$case->setId(...)->setOpenedDate(...)` and so on.
 */
class Application_Model_Impl_Case
{

    private $_caseId = null;
    
    private $_householdId = null;
    
    private $_openedUserId = null;

    private $_openedDate = null;

    private $_status = null;
    
    //Array of CaseNeed objects
    private $_caseNeeds = null;
    
    //Array of CaseVisit objects
    private $_visits = null;

    /* Generic get/set methods: */

    public function getId()
    {
        return $this->_caseId;
    }

    public function setId($id)
    {
        $this->_caseId = $id;
        return $this;
    }
    
    public function getHouseholdId(){
        return $this->_householdId;
    }
    
    public function setHouseholdId(){
        $this->_householdId;
        return $this;
    }
    
    public function getOpenedUserId(){
        return $this->_openedUserId;
    }
    
    public function setOpenedUserId($userid){
        $this->_openedUserId = $userid;
        return $this;
    }

    public function getOpenedDate()
    {
        return $this->_openedDate;
    }

    public function setOpenedDate($openedDate)
    {
        $this->_openedDate = $openedDate;
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function getCaseNeeds()
    {
        return $this->_caseNeeds;
    }

    public function setCaseNeeds($needs)
    {
        $this->_caseNeeds = $needs;
        return $this;
    }
    
    public function getVisits(){
        return $this->_visits;
    }
    
    public function setVisits($visits){
        $this->_visits = $visits;
        return $this;
    }
}
