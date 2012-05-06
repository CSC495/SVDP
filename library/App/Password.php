<?php

class App_Password
{
    public static function generatePassword($length)
    {
        srand(date("s"));
        $vals = "AaBbCcDdEeFfGgHhIiJjKkLlMmNnOoPpQqRrSsTtUuVvWwXxYyZz0123456789";
        
        $pass = "";
        
        while(strlen($pass) < $length){
            $pass .= substr($vals, rand() % (strlen($vals)),1);
        }
        
        // Ensure there's at least 1 digit to match password requirements
        // Not technically nessecary but nice to stay somewhat consistant
        if( !preg_match('([0-9]{1})',$pass) ){
            // Get a random digit
            $num = rand(0, 9);
            $index = rand() % strlen($pass);
            
            $sub1 = substr($pass, 0, $index);
            $sub2 = substr($pass,$index);
            
            $sub1 = $sub1 . $num;
            $pass = $sub1 . $sub2;
        }
        
        return $pass;
    }
}