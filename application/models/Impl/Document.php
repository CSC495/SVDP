<?php
/**
 * Class implements the model for a document
 */
class Application_Model_Impl_Document
{
    /**
     * The documents id in database
     * @var int
     */
    private $_id;
    /**
     * The location of the document
     * @var string
     */
    private $_url;
    /**
     * Display name of the document
     * @var string
     */
    private $_name;
    /**
     * Flag indicating if this is an uploaded file or not.
     * 1 means uploaded, 0 means doc is on another site
     * @var int
     */
    private $_internal;
    /**
     * Gets the ID of the document
     *
     * @return int
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
    * Gets the location of the document
    *
    * @return string
    */
    public function getUrl()
    {
        return $this->_url;
    }
    /**
    * Gets the display name of the document
    *
    * @return string
    */
    public function getName()
    {
        return $this->_name;
    }
    /**
    * Gets the flag to see if the document is internal or external
    *
    * @return int 1 if internal 0 if external
    */
    public function isInternal()
    {
        return $this->_internal;
    }
    /**
    * Sets the id of the document
    *
    * @param int $value The documents ID
    *
    * @return Application_Model_Impl_Document this
    */
    public function setId($value)
    {
        $this->_id = $value;
        return $this;
    }
    /**
     * Sets the url of the document
     *
     * @param string $value The documents url
     *
     * @return Application_Model_Impl_Document this
     */
    public function setUrl($value)
    {
        $this->_url = $value;
        return $this;
    }
    /**
     * Sets the display name for the document
     *
     * @param string $value The documents display name
     *
     * @return Application_Model_Impl_Document this
     */
    public function setName($value)
    {
        $this->_name = $value;
        return $this;
    }
    /**
     * Sets the flag if the document is internal or external
     *
     * @param int $value 1 if document is internal 0 if external
     *
     * @return Application_Model_Impl_Document this
     */
    public function setInternal($value)
    {
        $this->_internal = $value;
        return $this;
    }
}