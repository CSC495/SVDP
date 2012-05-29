<?php
/**
 * Class represents the information associated with a single case need.
 * Typically constitutes a single element of an array of other CaseNeeds
 * nested in a Case object
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$case->setId(...)->setOpenedDate(...)` and so on.
 */
class Application_Model_Impl_CaseNeed{
    private $_id = null;
    private $_need = null;
    private $_amount = null;
    private $_referralOrCheckReq = null;
    
    //Generic getter and setter methods
    public function getId(){
        return $this->_id;
    }
    
    public function setId($id){
        $this->_id = $id;
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

    public function hasReferralOrCheckReq(){
        return $this->_referralOrCheckReq !== null;
    }

    public function getReferralOrCheckReq(){
        return $this->_referralOrCheckReq;
    }

    public function setReferralOrCheckReq($referralOrCheckReq){
        $this->_referralOrCheckReq = $referralOrCheckReq;
        return $this;
    }
}
