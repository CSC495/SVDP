<?php

/**
 * Model class representing a member of some client's household (child, parent, sibling, roommate,
 * etc.)
 */
class Application_Model_Impl_Householder
{

    private $_id = null;

    private $_firstName = null;

    private $_lastName = null;

    private $_relationship = null;

    private $_birthDate = null;

    private $_departDate = null;

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

    public function getRelationship()
    {
        return $this->_relationship;
    }

    public function setRelationship($relationship)
    {
        $this->_relationship = $relationship;
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

    public function getDepartDate()
    {
        return $this->_departDate;
    }

    public function setDepartDate($departDate)
    {
        $this->_departDate = $departDate;
        return $this;
    }
}
