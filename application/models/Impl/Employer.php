<?php

/**
 * Model class representing a client's current or previous employer.
 */
class Application_Model_Impl_Employer
{

    private $_id = null;

    private $_company = null;

    private $_position = null;

    private $_startDate = null;

    private $_endDate = null;

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getCompany()
    {
        return $this->_company;
    }

    public function setCompany($company)
    {
        $this->_company = $company;
        return $this;
    }

    public function getPosition()
    {
        return $this->_position;
    }

    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }

    public function getStartDate()
    {
        return $this->_startDate;
    }

    public function setStartDate($startDate)
    {
        $this->_startDate = $startDate;
        return $this;
    }

    public function getEndDate()
    {
        return $this->_endDate;
    }

    public function setEndDate($endDate)
    {
        $this->_endDate = $endDate;
        return $this;
    }
}
