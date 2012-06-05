<?php
/**
 * Password validator which matches application specific password requirements
 */
class App_Validate_Password extends Zend_Validate_Abstract
{
	/**
     * Constant for defining which error occured
     * @var string
     */
    const MSG_MINIMUM = 'msgMinimum';
	/**
     * Constant for defining which error occured
     * @var string
     */
    const MSG_DIGIT   = 'msgDigit';
    
        /**
     * Constant for defining which error occured
     * @var string
     */
    const MSG_MAXIMUM = 'msgMaximum';
	/**
     * Constant for defining required password length
     * @var int
     */
    protected $_minimum = 8;
    
        /**
     * Constant for beginning max password length
     * @var int
     */
    protected $_maximum = 256;
    
	/**
     * Constant for defining message variables
     * @var array
     */
    protected $_messageVariables = array(
        'min' => '_minimum',
        'max' => '_maximum',
    );
	/**
     * Error messages if validation fails
     * @var array
     */
    protected $_messageTemplates = array(
        self::MSG_DIGIT   => "Password requires a single digit",
        self::MSG_MINIMUM => "Password must have a length of atleast %min%",
        self::MSG_MAXIMUM => "Password length cannot exceed %max% characters",
    );
	/**
     * Checks to see if a value is a valid password
	 *
	 * @param string $value Value to check
     * 
	 * @return bool True if valid false otherwise
     */
    public function isValid($value)
    {
        $this->_setValue($value);
        
        // Check if the password is less than the minimum length
        if( strlen($value) < $this->_minimum ){
            $this->_error(self::MSG_MINIMUM);
            return false;
        }
        
        if( strlen($value) > $this->_maximum ){
            $this->_error(self::MSG_MAXIMUM);
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
