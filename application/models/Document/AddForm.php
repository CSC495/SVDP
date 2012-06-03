<?php
/**
 * Class implements form for adding an External document by URL
 */
class Application_Model_Document_AddForm extends Twitter_Bootstrap_Form_Vertical {

	/**
	 * Default constructor
	 *
	 * @return null
	 */
    public function __construct()
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl(App_Resources::DOCUMENT) . '/add')
             ->setMethod('post');
        $this->setAttrib('class','twocol form-horizontal');
        $this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'document/addViewScript.phtml'))
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
		// Display name for doc
        $this->addElement('text', 'name', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'label'   => 'Name:',
            ));
        // URL to the document  
        $url = $this->addElement('text', 'url', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'label'   => 'Url:',
            'validators' => array( new App_Validate_Url()),
        ));
        
		// Submit button
        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'decorators' => array('ViewHelper'),
            'label'      => 'Add Document',
            'ignore'     => true
        ));


    }
}
