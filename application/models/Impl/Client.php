<?php

/**
 * Model class representing a client of the parish, containing basic get/set methods as well as some
 * convenience methods that format data for display in view scripts.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$client->setId(...)->setFirstName(...)` and so on.
 */
class Application_Model_Impl_Client
{

    private $_id = null;

    private $_userId = null;

    private $_firstName = null;

    private $_lastName = null;

    private $_otherName = null;

    private $_maritalStatus = null;

    private $_birthDate = null;

    private $_ssn4 = null;

    private $_cellPhone = null;

    private $_homePhone = null;

    private $_workPhone = null;

    private $_createdDate = null;

    private $_parish = null;

    private $_veteran = null;

    private $_spouse = null;

    private $_currentAddr = null;

    private $_doNotHelpReason = null;
    
    private $_employment = null;
    
    private $_hmembers = null;

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

    public function getUserId()
    {
        return $this->_userId;
    }

    public function setUserId($userId)
    {
        $this->_userId = $userId;
        return $this;
    }

    public function getFirstName()
    {
        return $this->_firstName;
    }

    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
        return $this;
    }

    public function getLastName()
    {
        return $this->_lastName;
    }

    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
        return $this;
    }

    public function getOtherName()
    {
        return $this->_otherName;
    }

    public function setOtherName($otherName)
    {
        $this->_otherName = $otherName;
        return $this;
    }

    public function getMaritalStatus()
    {
        return $this->_maritalStatus;
    }

    public function setMaritalStatus($maritalStatus)
    {
        $this->_maritalStatus = $maritalStatus;
        return $this;
    }

    public function getBirthDate()
    {
        return $this->_birthDate;
    }

    public function setBirthDate($birthDate)
    {
        $this->_birthDate = $birthDate;
        return $this;
    }

    public function getSsn4()
    {
        return $this->_ssn4;
    }

    public function setSsn4($ssn4)
    {
        $this->_ssn4 = $ssn4;
        return $this;
    }

    public function getCellPhone()
    {
        return $this->_cellPhone;
    }

    public function setCellPhone($cellPhone)
    {
        $this->_cellPhone = $cellPhone;
        return $this;
    }

    public function getHomePhone()
    {
        return $this->_homePhone;
    }

    public function setHomePhone($homePhone)
    {
        $this->_homePhone = $homePhone;
        return $this;
    }

    public function getWorkPhone()
    {
        return $this->_workPhone;
    }

    public function setWorkPhone($workPhone)
    {
        $this->_workPhone = $workPhone;
        return $this;
    }

    public function getCreatedDate()
    {
        return $this->_createdDate;
    }

    public function setCreatedDate($createdDate)
    {
        $this->_createdDate = $createdDate;
        return $this;
    }

    public function getParish()
    {
        return $this->_parish;
    }

    public function setParish($parish)
    {
        $this->_parish = $parish;
        return $this;
    }

    public function isVeteran()
    {
        return $this->_veteran;
    }

    public function setVeteran($veteran)
    {
        $this->_veteran = $veteran;
        return $this;
    }

    public function getSpouse()
    {
        return $this->_spouse;
    }

    public function setSpouse($spouse)
    {
        $this->_spouse = $spouse;
        return $this;
    }

    public function getCurrentAddr()
    {
        return $this->_currentAddr;
    }

    public function setCurrentAddr($currentAddr)
    {
        $this->_currentAddr = $currentAddr;
        return $this;
    }

    public function getDoNotHelpReason()
    {
        return $this->_doNotHelpReason;
    }

    public function setDoNotHelpReason($doNotHelpReason)
    {
        $this->_doNotHelpReason = $doNotHelpReason;
        return $this;
    }
    
    public function getEmployment(){
        return $this->_employment;
    }
    
    public function setEmployment($employ){
        $this->_employment = $employ;
        return $this;
    }
    
    public function getHouseMembers(){
        return $this->_hmembers;
    }
    
    public function setHouseMembers($hmembers){
        $this->_hmembers = $hmembers;
        return $this;
    }

    /* Less generic helper methods: */

    /**
     * Returns the full name of this client, i.e., the client's first name followed by the client's
     * last name. If either portion is `null`, it shall be omitted. If both names are `null`, the
     * empty string shall be returned.
     *
     * @return string
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

    /**
     * Returns a formatted version of this client's cell phone number, or the empty string if no
     * such number is set.
     *
     * @return string
     */
    public function getFormattedCellPhone()
    {
        return ($this->_cellPhone !== null) ? App_Formatting::formatPhone($this->_cellPhone) : '';
    }

    /**
     * Returns a formatted version of this client's home phone number, or the empty string if no
     * such number is set.
     *
     * @return string
     */
    public function getFormattedHomePhone()
    {
        return ($this->_homePhone !== null) ? App_Formatting::formatPhone($this->_homePhone) : '';
    }

    /**
     * Returns a formatted version of this client's work phone number, or the empty string if no
     * such number is set.
     *
     * @return string
     */
    public function getFormattedWorkPhone()
    {
        return ($this->_workPhone !== null) ? App_Formatting::formatPhone($this->_workPhone) : '';
    }

    /**
     * Returns a formatted version of the first of the following client values which is not `null`:
     * cell phone, home phone, and work phone. If all these values are null, the empty string shall
     * be returned.
     *
     * @return string
     */
    public function getFormattedPhone()
    {
        $cellPhone = $this->getFormattedCellPhone();
        $homePhone = $this->getFormattedHomePhone();
        $workPhone = $this->getFormattedWorkPhone();
        return ($cellPhone !== '') ? $cellPhone :
              (($homePhone !== '') ? $homePhone : $workPhone);
    }

    /**
     * Returns true if the client's marital status is "Married" and false otherwise.
     *
     * @return bool
     */
    public function isMarried()
    {
        return $this->_maritalStatus === 'Married';
    }

    /**
     * Returns true if the client has a "do not help" reason set and false otherwise.
     *
     * @return bool
     */
    public function isDoNotHelp()
    {
        return $this->_doNotHelpReason !== null;
    }
}
