<?php

class Application_Model_Impl_Document
{
    private $_id;
    private $_url;
    private $_name;
    private $_internal;
    
    public function getId()
    {
        return $this->_id;
    }
    
    public function getUrl()
    {
        return $this->_url;
    }
    
    public function getName()
    {
        return $this->_name;
    }
    
    public function isInternal()
    {
        return $this->_internal;
    }
    
    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }
    
    public function setUrl($value)
    {
        $this->_url = $value;
        return $this;
    }
    
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }
    
    public function setInternal($value)
    {
        $this->_internal == $value;
        return $this;
    }
}