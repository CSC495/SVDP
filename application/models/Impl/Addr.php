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
    /**
     * The unique id for this address
     * @var int
     */
    private $_id = null;

    /**
     * The street name and number for this address
     * @var string
     */
    private $_street = null;

    /**
     * The apartment number
     * @var string
     */
    private $_apt = null;

    /**
     * The city this address lies in
     * @var string
     */
    private $_city = null;

    /**
     * The state the address lies in
     * @var string
     */
    private $_state = null;

    /**
     * Zip code associated with address
     * @var int
     */
    private $_zip = null;

    /**
     * The parish attended while at address
     * @var string
     */
    private $_parish = null;

    /* Generic get/set methods: */

    /**
     * Gets the unique id for this address
     *
     * @return int id
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Sets the unique id for this address
     *
     * @param int $id id
     * 
     * @return Application_Model_Impl_Addr this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Returns the street for this address
     *
     * @return string Street
     */
    public function getStreet()
    {
        return $this->_street;
    }
    /**
     * Sets the street for this address
     *
     * @param string $street Street
     *
     * @return Application_Model_Impl_Addr this
     */
    public function setStreet($street)
    {
        $this->_street = $street;
        return $this;
    }
    /**
     * Gets the apartment number for this address
     *
     * @return string|null The apartment number or null
     */
    public function getApt()
    {
        return $this->_apt;
    }
    /**
     * Sets the apartment number for this address
     *
     * @param string $apt The apartment number
     *
     * @return Application_Model_Impl_Addr this
     */
    public function setApt($apt)
    {
        $this->_apt = $apt;
        return $this;
    }
    /**
     * Gets the city for this address
     *
     * @return string The city
     */
    public function getCity()
    {
        return $this->_city;
    }
    /**
     * Sets the city for this address
     *
     * @param string $city The city
     *
     * @return Application_Model_Impl_Addr this
     */
    public function setCity($city)
    {
        $this->_city = $city;
        return $this;
    }
    /**
     * Gets the state this address is in
     *
     * @return string The State
     */
    public function getState()
    {
        return $this->_state;
    }
    /**
     * Sets the state this address is in
     *
     * @param string $state The State address is in
     *
     * @return Application_Model_Impl_Addr this
     */
    public function setState($state)
    {
        $this->_state = $state;
        return $this;
    }
    /**
     * Gets the zip code for this address
     *
     * @return int zipcode for this address
     */
    public function getZip()
    {
        return $this->_zip;
    }
    /**
     * Sets the zip code for this address
     *
     * @param int $zip Zipcode
     *
     * @return Application_Model_Impl_Addr this
     */
    public function setZip($zip)
    {
        $this->_zip = $zip;
        return $this;
    }
    /**
     * Gets the parish this address attends
     *
     * @return string Parish attended at this address
     */
    public function getParish()
    {
        return $this->_parish;
    }
    /**
     * Sets the parish attended at this address
     *
     * @param string $parish Name of the parish
     *
     * @return Application_Model_Impl_Addr this
     */
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
