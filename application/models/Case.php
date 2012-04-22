<?php

class Application_Model_Case
{

    private $_id = null;

    private $_openedDate = null;

    private $_needList = null;

    private $_totalAmount = null;

    private $_client = null;

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
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

    public function getNeedList()
    {
        return $this->_needList;
    }

    public function setNeedList($needList)
    {
        $this->_needList = $needList;
        return $this;
    }

    public function getTotalAmount()
    {
        return $this->_totalAmount;
    }

    public function setTotalAmount($totalAmount)
    {
        $this->_totalAmount = $totalAmount;
        return $this;
    }

    public function getClient()
    {
        return $this->_client;
    }

    public function setClient($client)
    {
        $this->_client = $client;
        return $this;
    }
}
