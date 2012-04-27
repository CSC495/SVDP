<?php

/**
 * Model class representing a check request associated with some case need.
 *
 * Note: This class implements the fluent interface pattern, i.e., consecutive set method calls can
 * be chained together: `$case->setId(...)->setRequestDate(...)` and so on.
 */
class Application_Model_Impl_CheckReq
{

    private $_id = null;

    private $_requestDate = null;

    private $_case = null;

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

    public function getRequestDate()
    {
        return $this->_requestDate;
    }

    public function setRequestDate($requestDate)
    {
        $this->_requestDate = $requestDate;
        return $this;
    }

    public function getCase()
    {
        return $this->_case;
    }

    public function setCase($case)
    {
        $this->_case = $case;
        return $this;
    }
}
