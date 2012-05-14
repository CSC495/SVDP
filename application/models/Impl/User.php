<?php

class Application_Model_Impl_User
{
    private $_userId;
    
    private $_email;
    
    private $_lastName;
    
    private $_firstName;
    
    private $_cellPhone;
    
    private $_homePhone;
    
    private $_role;
    
    private $_active;
    
    private $_changePswdFlag;
    
    public function setUserId($userId){
        // User ids should not be changed.
        // if someone trys to set it just return without
        // modifying id. Should throw an exception?...
        if($this->_userId)
            return $this;
        
        $this->_userId = $userId;
        return $this;
    }
    
    public function getUserId(){
        return $this->_userId;
    }
    
    public function setEmail($email){
        $this->_email = $email;
        return $this;
    }
    
    public function getEmail(){
        return $this->_email;
    }
    
    public function setFirstName($fn){
        $this->_firstName = $fn;
        return $this;
    }
    
    public function getFirstName(){
        return $this->_firstName;
    }
    
    public function setLastName($ln){
        $this->_lastName = $ln;
        return $this;
    }
    
    public function getLastName(){
        return $this->_lastName;
    }
    public function setCellPhone($phone){
        $this->_cellPhone = $phone;
        return $this;
    }    
    public function getCellPhone(){
        return $this->_cellPhone;
    }
    
    public function setHomePhone($phone){
        $this->_homePhone = $phone;
        return $this;
    }
    
    public function getHomePhone(){
        return $this->_homePhone;
    }
    
    public function setRole($role){
        $this->_role = $role;
        return $this;
    }
    
    public function getRole(){
        return $this->_role;
    }
    
    public function setActive($val){
        $this->_active = $val;
        return $this;
    }
    
    public function getChangePswdFlag(){
        return $this->_changePswdFlag;
    }
    
    public function setChangePswdFlag($flag){
        $this->_changePswdFlag = $flag;
        return $this;
    }
    
    public function isActive(){
        if($this->_active == 1)
            return true;
        else
            return false;
    }
    public function getActive(){
        if(!$this->_active)
            return 0;
        return $this->_active;
    }
}