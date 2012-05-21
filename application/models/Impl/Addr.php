<?php

/**
 * Model class representing a client's home address. Contains basic get/set methods as well as some
 * convenience methods that format data for display in view scripts.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$address->setId(...)->setStreet(...)` and so on.
 */
class Application_Model_Impl_Addr
{

    private $_id = null;

    private $_street = null;

    private $_apt = null;

    private $_city = null;

    private $_state = null;

    private $_zip = null;

    private $_parish = null;

    /* Generic get/set methods: */

    public function getId()
    {
        return $this->_id;
    }

    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }

    public function getStreet()
    {
        return $this->_street;
    }

    public function setStreet($street)
    {
        $this->_street = $street;
        return $this;
    }

    public function getApt()
    {
        return $this->_apt;
    }

    public function setApt($apt)
    {
        $this->_apt = $apt;
        return $this;
    }

    public function getCity()
    {
        return $this->_city;
    }

    public function setCity($city)
    {
        $this->_city = $city;
        return $this;
    }

    public function getState()
    {
        return $this->_state;
    }


    public function setState($state)
    {
        $this->_state = $state;
        return $this;
    }

    public function getZip()
    {
        return $this->_zip;
    }

    public function setZip($zip)
    {
        $this->_zip = $zip;
        return $this;
    }

    public function getParish()
    {
        return $this->_parish;
    }

    public function setParish($parish)
    {
        $this->_parish = $parish;
        return $this;
    }

    /* Less generic helper methods: */

    /**
     * Returns a nicely formatted, one-line version of the full address.
     *
     * @return string
     */
    public function getFullAddr()
    {
        $part1 = implode(' ', array_filter(array(
            $this->_street,
            $this->_apt,
        ), 'Application_Model_Impl_Addr::isNotNullOrEmpty'));
        $part2 = implode(', ', array_filter(array(
            $this->_city,
            $this->_state,
        ), 'Application_Model_Impl_Addr::isNotNullOrEmpty'));
        $part3 = $this->_zip;
        return $part1
             . (($part1 !== null && $part2 !== null) ? ', ' : '')
             . $part2
             . (($part1 !== null || $part2 !== null) ? ' ' : '')
             . $part3;
    }

    /**
     * Returns `false` if the specified value is `null` or the empty string and `true` if it is not.
     *
     * @param mixed $x
     * @return bool
     */
    private static function isNotNullOrEmpty($x)
    {
        return $x !== null && $x !== '';
    }
}
