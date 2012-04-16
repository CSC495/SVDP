<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{

    public function init()
    {
        Zend_Session::start();
    }
}

