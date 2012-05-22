<?php

/**
 * Model class representing a referral of some case need to an external location.
 */
class Application_Model_Impl_Referral
{

    private $_id;

    private $_date;

    private $_reason;

    private $_referredTo;

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getDate()
    {
        return $this->_date;
    }

    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }

    public function getReason()
    {
        return $this->_reason;
    }

    public function setReason($reason)
    {
        $this->_reason = $reason;
        return $this;
    }

    public function getReferredTo()
    {
        return $this->_referredTo;
    }

    public function setReferredTo($referredTo)
    {
        $this->_referredTo = $referredTo;
        return $this;
    }
}
