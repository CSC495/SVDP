<?php

/**
 * Static helper methods that format various things in various ways for display or database storage.
 */
class App_Formatting
{

    /**
     * Formats a user role constant from `App_Roles` into a user-friendly string. Unrecognized roles
     * will be mapped to the empty string.
     *
     * @param string $role
     * @return string
     */
    public static function formatRole($role) {
        switch ($role) {
        case App_Roles::MEMBER:
            return 'Member';

        case App_Roles::TREASURER:
            return 'Treasurer';

        case App_Roles::ADMIN:
            return 'Admin';

        case App_Roles::DATAMIGRATION:
            return 'Data Migrator';

        default:
            return '';
        }
    }

    /**
     * Formats a 10-digit United States phone number. If the specified phone number is `null`, then
     * the empty string will be returned.
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
     * Formats a United States dollar amount. If the given amount is non-numeric, then the empty
     * string shall be returned.
     *
     * @param mixed $amount
     * @return string
     */
    public static function formatCurrency($amount)
    {
        return is_numeric($amount) ? number_format($amount, 2) : '';
    }

    /**
     * Formats a date from the database for display. If the specified date is `null`, then the empty
     * string will be returned.
     *
     * @param string $date
     * @return string
     */
    public static function formatDate($date)
    {
        return ($date !== null) ? date('m/d/Y', strtotime($date)) : '';
    }

    /**
     * Formats a date and time from the database for display. If the specified date and time is
     * `null`, then the empty string will be returned.
     *
     * @param string $dateTime
     * @return string
     */
    public static function formatDateTime($dateTime)
    {
        return ($dateTime !== null) ? date('m/d/Y g:i A', strtotime($dateTime)) : '';
    }

    /**
     * Formats a date for storage in the database. If the specified date is the empty string, then
     * `null` will be returned.
     *
     * @param string $date
     * @return string
     */
    public static function unformatDate($date)
    {
        return ($date !== '') ? date('Y-m-d', strtotime($date)) : null;
    }

    /**
     * Formats a date and time for storage in the database. If the specified date and time is the
     * empty string, then `null` will be returned.
     *
     * @param string $dateTime
     * @return string
     */
    public static function unformatDateTime($dateTime)
    {
        return ($dateTime !== '') ? date('Y-m-d H:i:s', strtotime($dateTime)) : null;
    }

    /**
     * Automatically chooses between singular and plural nouns to generate strings like "1 cow" vs.
     * "2 cows". By default, this function assumes that plurals are formed by appending an 's' to
     * the singular noun; however, a custom plural form may be provided for special cases (e.g.,
     * "1 cow" vs. "42 kine").
     *
     * @param int|double $num
     * @param string $singular
     * @param string|null $plural
     * @return string
     */
    public static function inflectPlural($num, $singular, $plural = null)
    {
        return "$num "
             . (($num == 1) ? $singular : (($plural !== null) ? $plural : "{$singular}s"));
    }

    /**
     * Truncates a string to the specific length, appending an ellipsis if truncation occurs.
     *
     * @param string $x
     * @param int $len
     * @return string
     */
    public static function truncateString($x, $len)
    {
        return (strlen($x) > $len) ? substr_replace($x, '…', $len) : $x;
    }

    /**
     * Converts the empty string to `null`, leaving other values unchanged.
     *
     * @param mixed $x
     * @return mixed
     */
    public static function emptyToNull($x)
    {
        return ($x !== '') ? $x : null;
    }

    /**
     * Checks if the specified string consists only of whitespace.
     *
     * @param string $x
     * @return bool
     */
    public static function isBlank($x)
    {
        return trim($x) === '';
    }
}
