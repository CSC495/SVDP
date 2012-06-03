<?php

/**
 * Service that looks up addresses using the Google Maps geocoding API.
 */
class App_Service_Map
{

    private $_addr = null;

    private $_coords = null;

    private $_errorMsg = null;

    /**
     * Instantiates a new `App_Service_Map` object, obtaining geocoding data for the specified
     * address.
     *
     * @param Application_Model_Impl_Addr $addr
     */
    public function __construct($addr)
    {
        // Prepare the geocoding request.
        $httpClient = new Zend_Http_Client('http://maps.googleapis.com/maps/api/geocode/json');
        $httpClient->setParameterGet(array(
            'sensor' => 'false',
            'address' => $addr->getFullAddr(),
        ));

        // Fetch and parse the geocoding data.
        $json = Zend_Json::decode($httpClient->request()->getBody(), Zend_Json::TYPE_OBJECT);

        // Handle errors.
        switch ($json->status) {
        case 'OK':
            break;

        case 'ZERO_RESULTS':
            return;

        case 'OVER_QUERY_LIMIT':
            $this->_errorMsg = 'Google API quota exceeded.';
            return;

        case 'REQUEST_DENIED':
            $this->_errorMsg = 'Google Maps geocoding request denied.';
            return;

        case 'INVALID_REQUEST':
            $this->_errorMsg = 'Invalid Google Maps geocoding query.';
            return;
        }

        // If we've made it this far, then we got at least one valid result, which we'll now turn
        // into an address object.
        $result = $json->results[0];

        $streetNum  = null;
        $streetName = null;
        $apt        = null;
        $city       = null;
        $state      = null;
        $zip        = null;

        // Process address components, which may be returned in any order, and any of which may be
        // missing.
        foreach ($result->address_components as $component) {
            if (in_array('street_number', $component->types, true)) {
                $streetNum = $component->long_name;
            } else if (in_array('route', $component->types, true)) {
                $streetName = $component->long_name;
            } else if (in_array('subpremise', $component->types, true)) {
                $apt = $component->long_name;
            } else if (in_array('locality', $component->types, true)) {
                $city = $component->long_name;
            } else if (in_array('administrative_area_level_1', $component->types, true)) {
                $state = $component->short_name;
            } else if (in_array('postal_code', $component->types, true)) {
                $zip = $component->short_name;
            }
        }

        $this->_addr = new Application_Model_Impl_Addr();
        $this->_addr
            ->setStreet(($streetNum !== null && $streetName !== null)
                ? $streetNum . ' ' . $streetName : $addr->getStreet())
            ->setApt($apt !== null ? $apt : $addr->getApt())
            ->setCity($city !== null ? $city : $addr->getCity())
            ->setState($state !== null ? $state : $addr->getState())
            ->setZip($zip !== null ? $zip : $addr->getZip());

        // Save GPS coordinates in their original format for easy consumption from JavaScript.
        $this->_coords = $result->geometry->location;
    }

    /**
     * Returns `true` if a geocoding result was successfully retrieved and `false` otherwise.
     *
     * @return bool
     */
    public function hasResult()
    {
        return $this->_addr !== null;
    }

    /**
     * Returns a reformatted/normalized address obtaining using the geocoding API, or `null` if no
     * such address could be obtained.
     *
     * @return Application_Model_Impl_Addr|null
     */
    public function getAddr()
    {
        return $this->_addr;
    }

    /**
     * Returns the latitude component of the request address, or `null` if no coordinates could be
     * obtained.
     *
     * @return float|null
     */
    public function getLatitude()
    {
        return $this->_coords->lat;
    }

    /**
     * Returns the longitude component of the request address, or `null` if no coordinates could be
     * obtained.
     *
     * @return float|null
     */
    public function getLongitude()
    {
        return $this->_coords->lng;
    }

    /**
     * Returns true if the geocoding request resulted in a Google Maps API error and false if not.
     *
     * @return bool
     */
    public function hasErrorMsg()
    {
        return $this->_errorMsg !== null;
    }

    /**
     * Returns an error message related to the geocoding query, or `null` if no errors occurred.
     *
     * @return string|null
     */
    public function getErrorMsg()
    {
        return $this->_errorMsg;
    }
}
