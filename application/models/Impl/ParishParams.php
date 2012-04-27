<?php

class Application_Model_Impl_ParishParams
{
    public $_fundsAvailable;
    
    public $_yearLimit;
    
    public $_fundLimit;
    
    public $_caseLimit;
    
    public function __construct($funds,$year,$fund,$case)
    {
        $this->_fundsAvailable = $funds;
        $this->_yearLimit = $year;
        $this->_fundLimit = $fund;
        $this->_caseLimit = $case;
    }
}