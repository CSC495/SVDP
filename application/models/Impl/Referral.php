<?php

/**
 * Model class representing a referral of some case need to an external location.
 */
class Application_Model_Impl_Referral
{
    /**
     * The unique id for this referral
     * @var int
     */
    private $_id;
    /**
     * The date of the referral was created/entered
     * @var string
     */
    private $_date;
    /**
     * The reason the referral was created/entered
     * @var string
     */
    private $_reason;
    /**
     * Name of the organization/entity referred to
     * @var string
     */
    private $_referredTo;

    /**
     * Gets the unique id of this referral
     *
     * @return int unique id
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Sets the unique id of the referral
     *
     * @param int $id unique id
     *
     * @return Application_Model_Impl_Referral this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Gets the date this referral was created/entered
     *
     * @return string The date created
     */
    public function getDate()
    {
        return $this->_date;
    }
    /**
     * Sets the date  this referral was created/entered
     *
     * @param string $date
     * @return Application_Model_Impl_Referral this
     */
    public function setDate($date)
    {
        $this->_date = $date;
        return $this;
    }
    /**
     * Gets the reason this referral was created/entered
     *
     * @return string Reason for referral
     */
    public function getReason()
    {
        return $this->_reason;
    }
    /**
     * Sets the reason this referral was created/entered
     *
     * @param string $reason Reason case referred
     * @return Application_Model_Impl_Referral this
     */
    public function setReason($reason)
    {
        $this->_reason = $reason;
        return $this;
    }
    /**
     * Gets who case was referred to
     *
     * @return string Reason referred
     */
    public function getReferredTo()
    {
        return $this->_referredTo;
    }
    /**
     * Sets who the case was referred to
     *
     * @param string $referredTo Entity/Organization case referred to
     * @return Application_Model_Impl_Referral this
     */
    public function setReferredTo($referredTo)
    {
        $this->_referredTo = $referredTo;
        return $this;
    }
}
