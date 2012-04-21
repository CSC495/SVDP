<?php

class Application_Model_TreasurerSearchForm extends Application_Model_SearchForm
{

    private $additionalTypes = array(
        'checkRequestId' => 'Check Request #',
    );

    public function __construct($options = null)
    {
        parent::__construct('treasurer', $additionalTypes, $options);
    }
}
