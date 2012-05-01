<?php

class Application_Model_Impl_ClientComment
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
    
    
    // Getters, possibly replace with 'unsafe' magical getter??
    public function getId()
    {
        return $this->_commentId;
    }

    public function getClientId()
    {
        return $this->_clientId;
    }
    
    public function getUserId()
    {
        return $this->_userId;
    }
    
    public function getCommentDate()
    {
        return $this->_commentDate;
    }
    
    public function getCommentId()
    {
        return $this->_comment;
    }
    
}