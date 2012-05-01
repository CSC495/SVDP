<?php

class Application_Model_Impl_ParishParams
{
    private $_fundsAvailable;
    
    private $_yearLimit;
    
    private $_lifeTimeLimit;
    
    private $_caseLimit;
    
    private $_caseFundLimit;
    
    public function __construct($funds,$year,$lifetime,$caseLimit,$caseFundLimit)
    {
        $this->_fundsAvailable = $funds;
        $this->_yearLimit = $year;
        $this->_lifeTimeLimit = $lifetime;
        $this->_caseLimit = $caseLimit;
        $this->_caseFundLimit = $caseFundLimit;
    }
    
    public function getAvailableFunds(){
        return $this->_fundsAvailable;
    }
    
    public function setAvailableFunds($amt){
        $this->_fundsAvailable = $amt;
        return $this;
    }
    
    public function getYearlyLimit(){
        return $this->_yearLimit;
    }
    
    public function setYearlyLimit($amt){
        $this->_yearLimit = $amt;
        return $this;
    }
    
    public function setLifeTimeLimit($amt){
        $this->_lifeTimeLimit = $amt;
        return $this;
    }
    
    public function getLifeTimeLimit(){
        return $this->_lifeTimeLimit;
    }
    
    public function setCaseLimit($amt){
        $this->_caseLimit = $amt;
        return $this;
    }
    
    public function getCaseLimit(){
        return $this->_caseLimit;
    }
    
    public function setCaseFundLimit($amt){
        $this->_caseFundLimit = $amt;
        return $this;
    }
    
    public function getCaseFundLimit(){
        return $this->_caseFundLimit;
    }
}