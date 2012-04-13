<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    public function init(){
        Zend_Session::start();
    }
    
    private function _initView(){
        $this->boostrap('view');
        $view = $this->getResource('view');
        $view->pageTitle('test');
    }
}

