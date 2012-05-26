<?php
/**
 * Class provides a model for a single comment
 */
class Application_Model_Impl_Comment
{
    /**
     * The unique id of this comment
     * @var int
     */
    private $_id;
    /**
     * The name of the user that left the comment
     * @var string
     */
    private $_user;
    /**
     * The date and time that the comment was made
     * @var string
     */
    private $_dateTime;
    /**
     * The text of the actual comment
     * @var string
     */
    private $_text;
    /**
     * Gets the unique id of this comment
     *
     * @return int Id of comment
     */
    public function getId()
    {
        return $this->_id;
    }
    /**
     * Sets the unique id for this comment
     *
     * @param int $id Id for this comment
     * @return Application_Model_Impl_Comment this
     */
    public function setId($id)
    {
        $this->_id = $id;
        return $this;
    }
    /**
     * Gets the name of the user that made this comment
     *
     * @return string Name of user who made comment
     */
    public function getUser()
    {
        return $this->_user;
    }
    /**
     * Sets the user who created the comment
     *
     * @param string $user Name of user who made comment
     * @return Application_Model_Impl_Comment this
     */
    public function setUser($user)
    {
        $this->_user = $user;
        return $this;
    }
    /**
     * Gets the date and time when the comment was made
     *
     * @return string Date and time comment was created formatted as a single string
     */
    public function getDateTime()
    {
        return $this->_dateTime;
    }
    /**
     * Sets the date and time the comment was created
     *
     * @param string $dateTime Date and time the comment was created formatted as a single string
     * @return Application_Model_Impl_Comment this
     */
    public function setDateTime($dateTime)
    {
        $this->_dateTime = $dateTime;
        return $this;
    }
    /**
     * Gets the actual text of the comment
     *
     * @return string The text of the comment
     */
    public function getText()
    {
        return $this->_text;
    }
    /**
     * Sets the text of the comment
     *
     * @param string $text The actual text of the comment
     * @return Application_Model_Impl_Comment this
     */
    public function setText($text)
    {
        $this->_text = $text;
        return $this;
    }
}
