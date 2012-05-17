<?php

class Application_Model_Impl_Comment
{

    private $_id;

    private $_user;

    private $_dateTime;

    private $_text;

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }

    public function getDateTime()
    {
        return $this->_dateTime;
    }

    public function setDateTime($dateTime)
    {
        $this->_dateTime = $dateTime;
        return $this;
    }

    public function getText()
    {
        return $this->_text;
    }

    public function setText($text)
    {
        $this->_text = $text;
        return $this;
    }
}
