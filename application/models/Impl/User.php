<?php
/**
 * Class provides the Model for a user 
 */
class Application_Model_Impl_User
{
    /**
     * The users unique ID
     * @var int
     */
    private $_userId;
    /**
     * The users email address
     * @var string
     */
    private $_email;
    /**
     * The users last name
     * @var string
     */
    private $_lastName;
    /**
     * The users first name
     * @var string
     */
    private $_firstName;
    /**
     * The users cell phone number
     * @var string
     */
    private $_cellPhone;
    /**
     * The users home phone number
     * @var string
     */
    private $_homePhone;
    /**
     * The users role
     * @var string
     */
    private $_role;
    /**
     * Flag if user is active or not
     * @var int
     */
    private $_active;
    /**
     * Flag if user needs to change their password
     * @var int
     */
    private $_changePswdFlag;
    /**
    * Function sets the users ID
    * 
    * @param string $userId the users id
    *
    * @return Application_Model_Impl_User this
    */
    public function setUserId($userId){
        $this->_userId = $userId;
        return $this;
    }
    /**
    * Function gets the users ID
    *
    * @return string The users id
    */
    public function getUserId(){
        return $this->_userId;
    }
    /**
    * Function sets the users emaill address
    *
    * @param string $email the users email
    * @return Application_Model_Impl_User this
    */
    public function setEmail($email){
        $this->_email = $email;
        return $this;
    }
    /**
    * Function gets the users email address
    *
    * @return string The users email
    */
    public function getEmail(){
        return $this->_email;
    }
    /**
    * Function sets the users first name
    *
    * @param string $fn The users first name
    *
    * @return Application_Model_Impl_User this
    */
    public function setFirstName($fn){
        $this->_firstName = $fn;
        return $this;
    }
    /**
    * Gets the users first name
    *
    * @return string Users first name
    */
    public function getFirstName(){
        return $this->_firstName;
    }
    /**
    * Sets the users last name
    *
    * @param string $ln User's last name
    *
    * @return Application_Model_Impl_User this
    */
    public function setLastName($ln){
        $this->_lastName = $ln;
        return $this;
    }
    /**
    * Returns the users last name
    *
    * @return string The users last name
    */
    public function getLastName(){
        return $this->_lastName;
    }
    /**
     * Sets the users cell phone number
     *
     * @param string $phone The users cell phone number
     *
     * @return Application_Model_Impl_User this
     */
    public function setCellPhone($phone){
        $this->_cellPhone = $phone;
        return $this;
    }  
    /**
     * Gets the users cell phone number
     *
     * @return string Users cell phone number
     */
    public function getCellPhone(){
        return $this->_cellPhone;
    }
    /**
     * Sets the users home phone number
     *
     * @param string $phone The users home phone number
     *
     * @return Application_Model_Impl_User this
     */
    public function setHomePhone($phone){
        $this->_homePhone = $phone;
        return $this;
    }
    /**
     * Gets the users home phone number
     *
     * @return string Users home phone number
     */
    public function getHomePhone(){
        return $this->_homePhone;
    }
    /**
     * Sets the users role
     *
     * @param string $role The users role
     *
     * @return Application_Model_Impl_User this
     */
    public function setRole($role){
        $this->_role = $role;
        return $this;
    }
    /**
     * Gets the users role
     *
     * @return string The users role
     */
    public function getRole(){
        return $this->_role;
    }
    /**
     * Sets the Active flag
     *
     * @param int $val The active flag
     *
     * @return Application_Model_Impl_User this
     */
    public function setActive($val){
        $this->_active = $val;
        return $this;
    }
    /**
     * Gets the change password flag
     *
     * @return int $flag change password flag
     */
    public function getChangePswdFlag(){
        return $this->_changePswdFlag;
    }
    /**
     * Sets the change password flag
     *
     * @param int $flag change password flag
     *
     * @return Application_Model_Impl_User this
     */
    public function setChangePswdFlag($flag){
        $this->_changePswdFlag = $flag;
        return $this;
    }
    /**
     * Gets the Active flag as boolean value
     *
     * @return bool True if user active false otherwise
     */
    public function isActive(){
        if($this->_active == 1)
            return true;
        else
            return false;
    }
    /**
     * Gets the Active flag as int value
     *
     * @return int 1 if user active 0 otherwise
     */
    public function getActive(){
        if(!$this->_active)
            return 0;
	else
	    return 1;
    }
    /**
     * Gets the users full name
     *
     * @return string First and last name appeneded together
     */
    public function getFullName()
    {
        if ($this->_firstName === null) {
            return (string) $this->_lastName;
        }
        if ($this->_lastName === null) {
            return (string) $this->_firstName;
        }
        return $this->_firstName . ' ' . $this->_lastName;
    }
}
