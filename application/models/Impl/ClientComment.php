<?php

class App_Models_Impl_ClientComment
{
    private $_commentId;
    
    private $_clientId;
    
    private $_userId;
    
    private $_commentDate;
    
    private $_comment;
    
    public function __construct($commentId, $clientId, $userId, $commentDate, $comment)
    {
        $this->_comment = $comment;
        $this->_clientId = $clientId;
        $this->_userId = $userId;
        $this->_commentDate = $commentDate;
        $this->_commentId = $commentId;
    }
    
    // Magical php getter
    public function __get($property){
        if(property_exists($this, $property)){
            return $this->$property;
        }
    }
    
}