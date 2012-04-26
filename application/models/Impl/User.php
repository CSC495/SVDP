<?php

class App_Models_Impl_User
{
    private $_userId;
    
    private $_email;
    
    private $_lastName;
    
    private $_firstName;
    
    private $_cellPhone;
    
    private $_homePhone;
    
    private $_role;
    
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