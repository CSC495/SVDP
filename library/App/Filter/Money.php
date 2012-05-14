<?php

class App_Filter_Money implements Zend_Filter_Interface
{
    public function filter($value){
        
        $filteredValue = preg_replace("/[\$]/",'',$value);
        
        // If regex fails return the original value
        if(!$filteredValue)
            return $value;
        else // Regex passed. Returned filtered value
            return $filteredValue;
    }
}