<?php

/**
 * Model class representing a client of the parish, containing basic get/set methods as well as some
 * convenience methods that format data for display in view scripts.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$client->setId(...)->setFirstName(...)` and so on.
 */
class Application_Model_Client
{

    private $_id = null;

    private $_firstName = null;

    private $_lastName = null;

    private $_cellPhone = null;

    private $_homePhone = null;

    private $_workPhone = null;

    private $_currentAddr = null;

    private $_doNotHelp = null;

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

    public function getCurrentAddr()
    {
        return $this->_currentAddr;
    }

    public function setCurrentAddr($currentAddr)
    {
        $this->_currentAddr = $currentAddr;
        return $this;
    }

    public function isDoNotHelp()
    {
        return $this->_doNotHelp;
    }

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
        return ($this->_cellPhone !== null) ? self::formatPhone($this->_cellPhone) : '';
    }

    /**
     * Returns a formatted version of this client's home phone number, or the empty string if no
     * such number is set.
     *
     * @return string
     */
    public function getFormattedHomePhone()
    {
        return ($this->_homePhone !== null) ? self::formatPhone($this->_homePhone) : '';
    }

    /**
     * Returns a formatted version of this client's work phone number, or the empty string if no
     * such number is set.
     *
     * @return string
     */
    public function getFormattedWorkPhone()
    {
        return ($this->_workPhone !== null) ? self::formatPhone($this->_workPhone) : '';
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
     * Formats a 10-digit United States phone number.
     *
     * @param string $phone
     * @return string
     */
    private static function formatPhone($phone)
    {
        $phone1 = substr($phone, 0, 3);
        $phone2 = substr($phone, 3, 3);
        $phone3 = substr($phone, 6, 4);
        return sprintf('(%s) %s-%s', $phone1, $phone2, $phone3);
    }
}
