<?php

class App_Validate_Password extends Zend_Validate_Abstract
{
    const MSG_MINIMUM = 'msgMinimum';
    const MSG_DIGIT   = 'msgDigit';
    
    public $minimum = 8;
    
    protected $_messageVariables = array(
        'min' => 'minimum',
    );
    
    protected $_messageTemplates = array(
        self::MSG_DIGIT   => "Password requires a single digit",
        self::MSG_MINIMUM => "Password must have a length of atleast '%min%'"
    );
    
    public function isValid($value)
    {
        $this->_setValue($value);
        
        // Check if the password is less than the minimum length
        if( strlen($value) < $this->minimum ){
            $this->_error(self::MSG_MINIMUM);
            return false;
        }
        
        // Check if password contains 1 digit
        if( !preg_match('([0-9]{1})',$value) ){
            $this->_error(self::MSG_DIGIT);
            return false;
        }
        
        return true;
    }
}