<?php

class App_Form_Element_Note extends Zend_Form_Element_Xhtml
{

    public $helper = 'formNote';

    public function isValid($data, $context = null)
    {
        return true;
    }
}
