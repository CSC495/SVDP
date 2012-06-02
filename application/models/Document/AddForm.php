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
	$this->setName('add');
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
	    'name'    => 'name',
	    'id'      => 'name',
	    'validators' => array(
			array('NotEmpty', true, array(
			    'type' => 'string',
			    'messages' => array('isEmpty' => 'File name must be provided'),
			)),
			array('StringLength', true, array(
			    'max' => 50,
			    'messages' => array(
				'stringLengthTooLong' => 'File name must not exceed 50 characters',
			    ),
			)),
		    ),
        ));
        // URL to the document  
        $url = $this->addElement('text', 'url', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'label'   => 'Url:',
	    'name'    => 'url',
	    'id'      => 'url',
            'validators' => array(
				  new App_Validate_Url(),
				array('StringLength', true, array(
				    'max' => 2083,
				    'messages' => array(
					'stringLengthTooLong' => 'URL cannot exceed 2083 characters',
				    ),
				)),
			    ),
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
