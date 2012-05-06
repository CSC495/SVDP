<?php

class Application_Model_Document_EditForm extends Twitter_Bootstrap_Form_Horizontal {

    public function __construct()
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl(App_Resources::MEMBER) . '/map')
             ->setMethod('get');

        $this->addElement('text', 'name', array(
            'required' => false,
            'filters' => array('StringTrim'),
            'label'  => 'File Name:',
            ));
        
        $this->addElement('text', 'url', array(
            'required' => false,
            'filters' => array('StringTrim'),
            'label'  => 'Url:',
            ));
    
        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
        ));


    }
}
