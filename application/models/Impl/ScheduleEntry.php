<?php

class Application_Model_Impl_ScheduleEntry
{

    private $_id = null;

    private $_startDate = null;

    private $_user = null;

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
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

    public function getUser()
    {
        return $this->_user;
    }

    public function setUser($user)
    {
        $this->_user = $user;
    }
}
