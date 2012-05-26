<?php

/**
 * Model class representing a client's current or previous employer.
 */
class Application_Model_Impl_Employer
{
    /**
     * The unique ID for this employer
     * @var int
     */
    private $_id = null;
    /**
     * The company the client worked for
     * @var string
     */
    private $_company = null;
    /**
     * The position the client had at the company
     * @var string
     */
    private $_position = null;
    /**
     * The day the client started at the company
     * @var string
     */
    private $_startDate = null;
    /**
     * The day the client ended employment at the company
     * @var string
     */ 
    private $_endDate = null;
    /**
     * Gets the unique id for this employment record
     * @return int Id for this employment record
     */
    public function getId()
    {
        return $this->_id;
    }   
    /**
     * Sets the unique id for this employment record
     *
     * @param int $id the id for this record
     * @return Application_Model_Impl_Employer this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Gets the company for this employment record
     *
     * @return string Name of company client worked for
     */
    public function getCompany()
    {
        return $this->_company;
    }
    /**
     * Set the company the client worked at
     *
     * @param string $company Company the client worked at
     * @return Application_Model_Impl_Employer this
     */
    public function setCompany($company)
    {
        $this->_company = $company;
        return $this;
    }
    /**
     * Gets the position the client had at this employer
     *
     * @return string The position client had at company
     */
    public function getPosition()
    {
        return $this->_position;
    }
    /**
     * Sets the position the client had at this employer
     *
     * @param string $position Position client had at employer
     * @return Application_Model_Impl_Employer this
     */
    public function setPosition($position)
    {
        $this->_position = $position;
        return $this;
    }
    /**
     * Gets the date that the client started at the employer
     *
     * @return string Date client started at the employer
     */
    public function getStartDate()
    {
        return $this->_startDate;
    }
    /**
     * Sets the date that the client started at the employer
     *
     * @param string $startDate The date the client started at employer
     * @return Application_Model_Impl_Employer this
     */
    public function setStartDate($startDate)
    {
        $this->_startDate = $startDate;
        return $this;
    }
    /**
     * Gets the date that this client ended employement
     *
     * @return string|null The date the client ended at employer or null if still employed
     */
    public function getEndDate()
    {
        return $this->_endDate;
    }
    /**
     * Sets the date the client ended employment
     *
     * @param string $endDate Date client ended employment
     * @return Application_Model_Impl_Employer this
     */
    public function setEndDate($endDate)
    {
        $this->_endDate = $endDate;
        return $this;
    }
}
