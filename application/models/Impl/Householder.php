<?php

/**
 * Model class representing a member of some client's household (child, parent, sibling, roommate,
 * etc.)
 */
class Application_Model_Impl_Householder
{

    /**
     * Unique id for this householder
     * @var int
     */
    private $_id = null;
    /**
     * First name of this householder
     * @var string
     */
    private $_firstName = null;
    /**
     * Last name of this householder
     * @var string
     */
    private $_lastName = null;
    /**
     * The relationship this householder has with the client
     * @var string
     */
    private $_relationship = null;
    /**
     * The birthdate of this householder
     * @var string
     */
    private $_birthDate = null;
    /**
     * The day this householder left the household
     * @var string
     */
    private $_departDate = null;
    /**
     * Gets the unique id for this householder
     *
     * @return int Householder's id
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Sets the householders unique id
     *
     * @param int $id Householder's id
     * @return Application_Model_Impl_Householder this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Gets the first name of the householder
     *
     * @return string The householders last name
     */
    public function getFirstName()
    {
        return $this->_firstName;
    }
    /**
     * Sets the first name of the householder
     *
     * @param string $firstName Householders first name
     * @return Application_Model_Impl_Householder this
     */
    public function setFirstName($firstName)
    {
        $this->_firstName = $firstName;
        return $this;
    }
    /**
     * Gets the householders last name
     *
     * @return string The householders last name
     */
    public function getLastName()
    {
        return $this->_lastName;
    }
    /**
     * Sets the last name of the householder
     *
     * @param string $lastname Sets the lastname of the householder
     * @return Application_Model_Impl_Householder this
     */
    public function setLastName($lastName)
    {
        $this->_lastName = $lastName;
        return $this;
    }
    /**
     * Gets the relationship of this householder to the client
     *
     * @return string the householders relationship with the client
     */
    public function getRelationship()
    {
        return $this->_relationship;
    }
    /**
     * Sets the relationship of this householder to the client
     *
     * @param string $relationship The householders relation with the client
     * @return Application_Model_Impl_Householder this
     */
    public function setRelationship($relationship)
    {
        $this->_relationship = $relationship;
        return $this;
    }
    /**
     * Gets the householders birth date
     *
     * @return string The birthdate of this householder
     */
    public function getBirthDate()
    {
        return $this->_birthDate;
    }
    /**
     * Sets the birthdate of the householder
     *
     * @param $birthDate The householders birthdate
     * @return Application_Model_Impl_Householder this
     */
    public function setBirthDate($birthDate)
    {
        $this->_birthDate = $birthDate;
        return $this;
    }
    /**
     * Gets the date the householder departed/left the household
     *
     * @return string Date the householder departed the household
     */
    public function getDepartDate()
    {
        return $this->_departDate;
    }
    /**
     * Sets the date the householder departed/left the household
     *
     * @param string $departDate Date householder departed the household
     * @return Application_Model_Impl_Householder this
     */
    public function setDepartDate($departDate)
    {
        $this->_departDate = $departDate;
        return $this;
    }
    
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
}
