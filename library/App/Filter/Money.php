<?php
/**
 * Filter which extracts the dollar sign from the value
 */
class App_Filter_Money implements Zend_Filter_Interface
{
	/**
     * Removes the dollar sign from the value
	 *
	 * @param string $value Value to filter
     * 
	 * @return string Value with single dollar sign removed
     */
    public function filter($value){
        
        $filteredValue = preg_replace("/[\$]/",'',$value);
        
        // If regex fails return the original value
        if(!$filteredValue)
            return $value;
        else // Regex passed. Returned filtered value
            return $filteredValue;
    }
}