<?php

/**
 * This class---which contains only static methods and should not be instantiated---holds helper
 * functions for performing various special character escapes not supported directly by PHP or Zend
 * Framework.
 */

class App_Escaping
{
    /**
     * Escapes wildcards in SQL LIKE expressions. This shouldn't have any immediate security
     * benefit, but it prevents users from being exposed to potentially confusing behavior.
     *
     * @param string $what The string to escape.
     *
     * @return string The escaped string.
     */
    public static function escapeLike($what)
    {
        return addcslashes($what, '\\%_');
    }
}
