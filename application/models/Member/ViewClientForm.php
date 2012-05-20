<?php

class Application_Model_Member_ViewClientForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function __construct(Application_Model_Impl_Client $client, array $cases)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'action' => $baseUrl->baseUrl(
                App_Resources::MEMBER . '/viewCase/id/' . urlencode($client->getId())
            ),
            'method' => 'post',
            'class' => 'form-horizontal',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/view-client-form.phtml',
                    'client' => $client,
                    'cases' => $cases,
                )),
                'Form',
            ),
        ));
    }
}
