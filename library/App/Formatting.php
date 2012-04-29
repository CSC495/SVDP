<?php

class App_Formatting
{

    /**
     * Formats a 10-digit United States phone number.
     *
     * @param string $phone
     * @return string
     */
    public static function formatPhone($phone)
    {
        $phone1 = substr($phone, 0, 3);
        $phone2 = substr($phone, 3, 3);
        $phone3 = substr($phone, 6, 4);
        return sprintf('(%s) %s-%s', $phone1, $phone2, $phone3);
    }

    private static function emptyToNull($x)
    {
        return ($x !== '') ? $x : null;
    }
}
