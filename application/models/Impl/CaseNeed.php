<?php
//Class represents the information associated with a single case need.
//Typically constitutes a single element of an array of other CaseNeeds
//nested in a Case object
//Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
//be chained together: `$case->setId(...)->setOpenedDate(...)` and so on.
class Application_Model_Impl_CaseNeed{
    private $_caseNeedId = null;
    private $_need = null;
    private $_amount = null;
    
    //Generic getter and setter methods
    public function getCaseNeedId(){
        return $this->_caseNeedId;
    }
    
    public function setCaseNeedId($id){
        $this->_caseNeedId = $id;
        return $this;
    }
    
    public function getNeed(){
        return $this->_need;
    }
    
    public function setNeed($need){
        $this->_need = $need;
        return $this;
    }
    
    public function getAmount(){
        return $this->_amount;
    }
    
    public function setAmount($amount){
        $this->_amount = $amount;
        return $this;
    }
}