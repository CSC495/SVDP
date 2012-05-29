<?php

/**
 * A simple extension of `Zend_Validate_GreaterThan` that allows the input being validating to equal
 * the specified minimum value.
 */
class App_Validate_GreaterThanOrEqualTo extends Zend_Validate_GreaterThan
{

    /**
     * Returns `true` if the specified value is valid and `false` if not.
     *
     * @param mixed $value
     * @return bool
     */
    public function isValid($value)
    {
        $this->_setValue($value);

        if ($this->_min > $value) {
            $this->_error(self::NOT_GREATER);
            return false;
        }

        return true;
    }
}
