<?php

class App_Formatting
{

    /**
     * Formats a user role constant from `App_Roles` into a user-friendly string. Unrecognized roles
     * will be mapped to the empty string.
     */
    public static function formatRole($role) {
        switch ($role) {
        case App_Roles::MEMBER:
            return 'Member';

        case App_Roles::TREASURER:
            return 'Treasurer';

        case App_Roles::ADMIN:
            return 'Admin';

        default:
            return '';
        }
    }

    /**
     * Formats a 10-digit United States phone number.
     *
     * @param string $phone
     * @return string
     */
    public static function formatPhone($phone)
    {
        if($phone === null || $phone === '')
            return '';
        
        $phone1 = substr($phone, 0, 3);
        $phone2 = substr($phone, 3, 3);
        $phone3 = substr($phone, 6, 4);
        return sprintf('(%s) %s-%s', $phone1, $phone2, $phone3);
    }

    /**
     * Formats a United States dollar amount.
     *
     * @param float $amount
     * @return string
     */
    public static function formatCurrency($amount)
    {
        return '$' . number_format($amount, 2);
    }

    public static function formatDate($date)
    {
        return ($date !== null) ? date('m/d/Y', strtotime($date)) : '';
    }

    public static function formatDateTime($dateTime)
    {
        return ($dateTime !== null) ? date('m/d/Y g:i A', strtotime($dateTime)) : '';
    }

    public static function unformatDate($date)
    {
        return ($date !== '') ? date('Y-m-d', strtotime($date)) : null;
    }

    public static function unformatDateTime($dateTime)
    {
        return ($dateTime !== null) ? date('Y-m-d H:i:s', strtotime($dateTime)) : '';
    }

    /**
     * Truncates a string to the specific length, appending an ellipsis if truncate occurs.
     *
     * @param string $x
     * @return string
     */
    public static function truncateString($x)
    {
        return (strlen($x) > 18) ? substr_replace($x, '…', 18) : $x;
    }

    public static function emptyToNull($x)
    {
        return ($x !== '') ? $x : null;
    }

    public static function isBlank($x)
    {
        return trim($x) === '';
    }
}
