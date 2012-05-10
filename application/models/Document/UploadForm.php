<?php

class Application_Model_Document_UploadForm extends Twitter_Bootstrap_Form_Vertical {

    public function __construct()
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl(App_Resources::ADMIN) . '/upload')
             ->setMethod('post');
        $this->setAttrib('class','twocol form-horizontal');
        $this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'document/uploadViewScript.phtml'))
		));
             
        $this->addElementPrefixPath(
                   'Twitter_Bootstrap_Form_Decorator',
                   'Twitter/Bootstrap/Form/Decorator',
                   'decorator'
        );
        
        $this->setElementDecorators(array(
            'FieldSize',
            'ViewHelper',
            'Addon',
            'ElementErrors',
            array('Description', array('class' => 'help-block')),
            array('HtmlTag', array('tag' => 'div', 'class' => 'controls')),
            array('Label', array('class' => 'control-label')),
            'Wrapper',
        ));

        $this->addElement('text', 'name', array(
            'required' => false,
            'filters' => array('StringTrim'),
            'label'  => 'File Name:',
            ));
        
        $this->addElement('file', 'url', array(
            'required' => false,
            'filters' => array('StringTrim'),
            'label'  => 'Url:',
            'decorators' => array('ViewHelper'),
        ));
        
        
        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'decorators' => array('ViewHelper'),
        ));


    }
}
