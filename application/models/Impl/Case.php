<?php

/**
 * Model class representing a single case, which belongs to some client and which is assigned to
 * some parish member.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$case->setId(...)->setOpenedDate(...)` and so on.
 */
class Application_Model_Impl_Case
{

    private $_id = null;

    private $_openedUser = null;

    private $_openedDate = null;

    private $_status = null;

    private $_totalAmount = null;

    private $_needList = null;

    //Array of CaseNeed objects
    private $_needs = null;

    //Array of CaseVisit objects
    private $_visits = null;

    //Client object?
    private $_client = null;

    //Array of Comment objects
    private $_comments = null;

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

    public function getOpenedUser(){
        return $this->_openedUser;
    }

    public function setOpenedUser($user){
        $this->_openedUser = $user;
        return $this;
    }

    public function getOpenedDate()
    {
        return $this->_openedDate;
    }

    public function setOpenedDate($openedDate)
    {
        $this->_openedDate = $openedDate;
        return $this;
    }

    public function getStatus()
    {
        return $this->_status;
    }

    public function setStatus($status)
    {
        $this->_status = $status;
        return $this;
    }

    public function getNeeds()
    {
        return $this->_needs;
    }

    public function setNeeds($needs)
    {
        $this->_needs = $needs;

        $needAmounts      = array();
        $needDescriptions = array();

        foreach ($needs as $need) {
            $needAmounts[]      = $need->getAmount();
            $needDescriptions[] = $need->getNeed();
        }

        $this->_totalAmount = number_format(array_sum($needAmounts), 2, '.', '');
        $this->_needList    = implode(', ', $needDescriptions);
    }

    public function getNeedList()
    {
        return $this->_needList;
    }

    public function setNeedList($needList)
    {
        $this->_needList = $needList;
        return $this;
    }

    public function getVisits(){
        return $this->_visits;
    }

    public function setVisits($visits){
        $this->_visits = $visits;
        return $this;
    }

    public function getTotalAmount(){
        return $this->_totalAmount;
    }

    public function setTotalAmount($totalAmount){
        $this->_totalAmount = $totalAmount;
        return $this;
    }

    public function getClient(){
        return $this->_client;
    }

    public function setClient($client){
        $this->_client = $client;
        return $this;
    }

    public function getComments(){
        return $this->_comments;
    }

    public function setComments($comments){
        $this->_comments = $comments;
        return $this;
    }
}
