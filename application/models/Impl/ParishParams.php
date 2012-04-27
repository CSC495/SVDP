<?php

class App_Models_Impl_ParishParams
{
    private $_fundsAvailable;
    
    private $_yearLimit;
    
    private $_fundLimit;
    
    private $_caseLimit;
    
    
    // Magical php getter
    public function __get($property){
        if(property_exists($this, $property)){
            return $this->$property;
        }
    }
    
    // Magical php setter
    public function __set($property,$value){
        if(property_exists($this, $property)){
            $this->$property = $value;
        }
        return($this);
    }
    
    
}