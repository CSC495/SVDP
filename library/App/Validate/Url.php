<?php
/**
 * URL Validator which utilizes Zend_Uri::check
 */
class App_Validate_Url extends Zend_Validate_Abstract
{
	/**
     * Constant for defining which error occured
     * @var string
     */
    const INVALID_URL = 'invalidUrl';
    /**
     * Error messages if validation fails
     * @var array
     */
    protected $_messageTemplates = array(
        self::INVALID_URL => "'%value%' is not a valid URL.",
    );
	/**
     * Checks to see if a value is a valid URL
	 *
	 * @param string $value Value to check
     * 
	 * @return bool True if valid false otherwise
     */
    public function isValid($value)
    {
        $valueString = (string) $value;
        $this->_setValue($valueString);
        
        if (!Zend_Uri::check($value))
        {
            $this->_error(self::INVALID_URL);
            return false;
        }
        
        return true;
    }
}