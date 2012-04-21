<?php

class Application_Model_MemberSearchForm extends Application_Model_SearchForm
{

    public function __construct($options = null)
    {
        parent::__construct('member', array(), $options);
    }
}
