<?php
/**
 * Class utilities for things pertaining to passwords including SALT and password generation
 */
class App_Password
{
	/**
	 * Generates a random password of specifed length.
	 * ensures password contains atleast 1 digit
	 *
	 * @param int $length Length of password to generate
	 *
	 * @return string Generated password
	 */
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
            
			// replace a random index with the generated number
            $sub1 = substr($pass, 0, $index);
            $sub2 = substr($pass,$index);
            
            $sub1 = $sub1 . $num;
            $pass = $sub1 . $sub2;
        }
        
        return $pass;
    }
	/**
	 * Returns a new string that has the salt added to it
	 *
	 * @param string $string Value to prepend the salt to
	 *
	 * @return string A new string with the salt prepended
	 */
    public static function saltIt($string)
    {
        $salt = 'tIHn1G$0 d1F5r 3tyHW33 tnR1uN5jt@ L@8';
        
        $new = $salt . $string;
        
        return $new;
    }
}