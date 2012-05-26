<?php
/**
 * Class provides a model which implements a single entry
 * in the schedule.
 */
class Application_Model_Impl_ScheduleEntry
{
    /**
     * The unique id of this entry
     * @var int
     */
    private $_id = null;
    /**
     * Start date of shift
     * @var string
     */
    private $_startDate = null;
    /**
     * Name of the user who will be working
     * @var string
     */
    private $_user = null;
    /**
     * Gets the unique id of this entry
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Sets the unique id of this entry
     *
     * @param int $id
     * @return Application_Model_Impl_ScheduleEntry this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Gets the start date for this entry
     *
     * @return string Start date
     */
    public function getStartDate()
    {
        return $this->_startDate;
    }
    /**
     * Sets the start date for this entry
     *
     * @param string $startDate the start date
     * @return Application_Model_Impl_ScheduleEntry this
     */
    public function setStartDate($startDate)
    {
        $this->_startDate = $startDate;
        return $this;
    }
    /**
     * Gets the name of the user working on this entry
     *
     * @return string Users name
     */
    public function getUser()
    {
        return $this->_user;
    }
    /**
     * Sets the name of the user who works on this entry
     *
     * @param string $user The name of the user
     * @return Application_Model_Impl_ScheduleEntry this
     */
    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }
}
