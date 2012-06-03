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
     * Constant for defining required password length
     * @var string
     */
    const $minimum = 8;
    
	/**
     * Constant for defining message variables
     * @var array
     */
    protected $_messageVariables = array(
        'min' => 'minimum',
    );
	/**
     * Error messages if validation fails
     * @var array
     */
    protected $_messageTemplates = array(
        self::MSG_DIGIT   => "Password requires a single digit",
        self::MSG_MINIMUM => "Password must have a length of atleast '%min%'"
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