<?php

class MemberController extends Zend_Controller_Action
{

    public function indexAction()
    {
        $this->view->pageTitle = "Open Cases";

        $service           = new App_Service_Search();
        $userId            = Zend_Auth::getInstance()->getIdentity()->user_id;
        $this->view->cases = $service->getOpenCasesByUserId($userId);
    }
}
