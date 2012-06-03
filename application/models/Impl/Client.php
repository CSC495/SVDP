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
    /**
     * Unique id of the client
     * @var int
     */
    private $_id = null;
    /**
     * User associated with client
     * @var Application_Model_Impl_Client|null
     */
    private $_user = null;
    /**
     * First name of the client
     * @var string
     */
    private $_firstName = null;
    /**
     * Last name of the client
     * @var string
     */
    private $_lastName = null;
    /**
     * Nickname of client if any
     * @var string
     */
    private $_otherName = null;
    /**
     * The marital status of the client
     * @var string
     */
    private $_maritalStatus = null;
    /**
     * The birthdate of the client
     * @string
     */
    private $_birthDate = null;
    /**
     * The last 4 digits of the clients SSN
     * @var int
     */
    private $_ssn4 = null;
    /**
     * The clients cellphone number
     * @var string|null
     */
    private $_cellPhone = null;
    /**
     * The clients homephone number
     * @var string|null
     */
    private $_homePhone = null;
    /**
     * The clients workphone number
     * @var string|null
     */
    private $_workPhone = null;
    /**
     * The date the client was created
     * @var string
     */
    private $_createdDate = null;
    /**
     * The parish the client attends
     * @var string
     */
    private $_parish = null;
    /**
     * Flag if client is a veteran or not
     * @var bool
     */
    private $_veteran = null;
    /**
     * The clients spouse if any
     * @var Application_Model_Impl_Client|null
     */
    private $_spouse = null;
    /**
     * The unique id of the household
     * @var int
     */
    private $_householdId = null;
    /**
     * The clients current address
     * @var Application_Model_Impl_Addr
     */
    private $_currentAddr = null;
    /**
     * The client's do-not-help list entry, if any
     * @var Application_Model_Impl_DoNotHelp|null
     */
    private $_doNotHelp = null;

    /* Generic get/set methods: */

    /**
     * Gets the clients unique Id
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Sets the users unique id
     *
     * @param int $id
     * @return Application_Model_Impl_Client this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Gets the user associated with this client
     *
     * @return Application_Model_Impl_User
     */
    public function getUser()
    {
        return $this->_user;
    }
    /**
     * Sets the user associated with this client
     *
     * @param Application_Model_Impl_User $user
     * @return Application_Model_Impl_Client this
     */
    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }
    /**
     * Gets the users first name
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }
    /**
     * Sets the users first name
     *
     * @param string %firstName
     * @return Application_Model_Impl_Client this
     */
    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
        return $this;
    }
    /**
     * Gets the clients last name
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->_lastName;
    }
    /**
     * Sets the clients last name
     *
     * @param string $lastName
     * @return Application_Model_Impl_Client this
     */
    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
        return $this;
    }
    /**
     * Gets the clients nickname
     *
     * @return string|null Other name or null if none
     */
    public function getOtherName()
    {
        return $this->_otherName;
    }
    /**
     * Sets the clients other name
     *
     * @param string $otherName
     * @return Application_Model_Impl_Client this
     */
    public function setOtherName($otherName)
    {
        $this->_otherName = $otherName;
        return $this;
    }
    /**
     * Gets the clients marital status
     *
     * @return string
     */
    public function getMaritalStatus()
    {
        return $this->_maritalStatus;
    }
    /**
     * Sets the clients marital status
     *
     * @param string $maritalStatus
     * @return Application_Model_Impl_Client this
     */
    public function setMaritalStatus($maritalStatus)
    {
        $this->_maritalStatus = $maritalStatus;
        return $this;
    }
    /**
     * Gets the clients birthdate
     *
     * @return string
     */
    public function getBirthDate()
    {
        return $this->_birthDate;
    }
    /**
     * Sets the birthdate of the client
     *
     * @param string $birthDate
     * @return Application_Model_Impl_Client this
     */
    public function setBirthDate($birthDate)
    {
        $this->_birthDate = $birthDate;
        return $this;
    }
    /**
     * Gets the last four digits of the clients SSN
     *
     * @return int
     */
    public function getSsn4()
    {
        return $this->_ssn4;
    }
    /**
     * Sets the last four digits of the clients SSN
     *
     * @param int $ssn4
     * @return Application_Model_Impl_Client this
     */
    public function setSsn4($ssn4)
    {
        $this->_ssn4 = $ssn4;
        return $this;
    }
    /**
     * Gets the clients cell phone number if any
     *
     * @return string|null Cell number or null if none
     */
    public function getCellPhone()
    {
        return $this->_cellPhone;
    }
    /**
     * Sets the clients cell phone number
     *
     * @param string $cellPhone
     * @return Application_Model_Impl_Client this
     */
    public function setCellPhone($cellPhone)
    {
        $this->_cellPhone = $cellPhone;
        return $this;
    }
    /**
     * Gets the clients home phone number if any
     *
     * @return string|null Home number or null if none
     */
    public function getHomePhone()
    {
        return $this->_homePhone;
    }
    /**
     * Sets the clients home phone number
     *
     * @param string $homePhone
     * @return Application_Model_Impl_Client this
     */
    public function setHomePhone($homePhone)
    {
        $this->_homePhone = $homePhone;
        return $this;
    }
    /**
     * Gets the clients work phone number
     *
     * @return string|null Work number or null if none
     */
    public function getWorkPhone()
    {
        return $this->_workPhone;
    }
    /**
     * Sets the clients work phone number
     *
     * @param string $workPhone
     * @return Application_Model_Impl_Client this
     */
    public function setWorkPhone($workPhone)
    {
        $this->_workPhone = $workPhone;
        return $this;
    }
    /**
     * Gets the date the client was created
     *
     * @return string
     */
    public function getCreatedDate()
    {
        return $this->_createdDate;
    }
    /**
     * Sets the date the client was created
     *
     * @param string $createdDate
     * @return Application_Model_Impl_Client this
     */
    public function setCreatedDate($createdDate)
    {
        $this->_createdDate = $createdDate;
        return $this;
    }
    /**
     * Gets the parish the client attends
     *
     * @return string
     */
    public function getParish()
    {
        return $this->_parish;
    }
    /**
     * Sets the parish the client attends
     *
     * @param string $parish
     * @return Application_Model_Impl_Client this
     */
    public function setParish($parish)
    {
        $this->_parish = $parish;
        return $this;
    }
    /**
     * Checks if the client is a veteran
     *
     * @return bool True if client is veteran false otherwise
     */
    public function isVeteran()
    {
        return $this->_veteran;
    }
    /**
     * Sets if the client is a veteran
     *
     * @param bool $veteran
     * @return Application_Model_Impl_Client this
     */
    public function setVeteran($veteran)
    {
        $this->_veteran = $veteran;
        return $this;
    }
    /**
     * Gets the clients spouse if any
     *
     * @return Application_Model_Impl_Client|null null if no spouse
     */
    public function getSpouse()
    {
        return $this->_spouse;
    }
    /**
     * Sets the clients spouse
     *
     * @param Application_Model_Impl_Client $spouse
     * @return Application_Model_Impl_Client this
     */
    public function setSpouse($spouse)
    {
        $this->_spouse = $spouse;
        return $this;
    }
    /**
     * Gets the id of this clients household
     *
     * @return int
     */
    public function getHouseholdId()
    {
        return $this->_householdId;
    }
    /**
     * Sets the household id of this clients household
     *
     * @param int $householdId
     * @return Application_Model_Impl_Client this
     */
    public function setHouseholdId($householdId)
    {
        $this->_householdId = $householdId;
        return $this;
    }
    /**
     * Gets the clients Address
     *
     * @return Application_Model_Impl_Addr
     */
    public function getCurrentAddr()
    {
        return $this->_currentAddr;
    }
    /**
     * Sets the clients current address
     *
     * @param Application_Model_Impl_Addr $currentAddr
     * @return Application_Model_Impl_Client this
     */
    public function setCurrentAddr($currentAddr)
    {
        $this->_currentAddr = $currentAddr;
        return $this;
    }
    /**
     * Gets the client's do-not-help list entry, if any
     *
     * @return string|null null if not on do not help list or entry on list
     */
    public function getDoNotHelp()
    {
        return $this->_doNotHelp;
    }
    /**
     * Sets the client's do-not-help list entry
     *
     * @param Application_Model_Impl_DoNotHelp|null $doNotHelp
     * @return Application_Model_Impl_Client this
     */
    public function setDoNotHelp($doNotHelp)
    {
        $this->_doNotHelp = $doNotHelp;
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
        return $this->_doNotHelp !== null;
    }
}
