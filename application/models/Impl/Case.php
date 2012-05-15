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

    private $_id = null;
    
    private $_householdId = null;
    
    private $_openedUserId = null;

    private $_openedDate = null;

    private $_status = null;
    
    private $_totalAmount = null;
    
    //Array of CaseNeed objects
    private $_needList = null;
    
    //Array of CaseVisit objects
    private $_visits = null;
    
    //Client object?
    private $_client = null;

    /* Generic get/set methods: */

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    
    public function getHouseholdId(){
        return $this->_householdId;
    }
    
    public function setHouseholdId($householdId){
        $this->_householdId = $householdId;
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

    public function getNeedList()
    {
        return $this->_needList;
    }

    public function setNeedList($needList)
    {
        $this->_needList = $needList;
        return $this;
    }
    
    public function getVisits(){
        return $this->_visits;
    }
    
    public function setVisits($visits){
        $this->_visits = $visits;
        return $this;
    }
    
    public function getTotalAmount(){
        return $this->_totalAmount;
    }
    
    public function setTotalAmount($totalAmount){
        $this->_totalAmount = $totalAmount;
        return $this;
    }
    
    public function getClient(){
        return $this->_client;
    }
    
    public function setClient($client){
        $this->_client = $client;
        return $this;
    }
}
