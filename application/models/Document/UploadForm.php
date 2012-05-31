<?php
/**
 * Class implements the form to upload a document to the server
 */
class Application_Model_Document_UploadForm extends Twitter_Bootstrap_Form_Vertical {
	/**
	 * Default constructor
	 *
	 * @return null
	 */
    public function __construct()
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl(App_Resources::DOCUMENT) . '/upload')
             ->setMethod('post');
        $this->setAttrib('class','twocol form-horizontal');
        $this->setName('upload');
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

		// Display name for doc
        $this->addElement('text', 'name', array(
            'required' => true,
            'filters' => array('StringTrim'),
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
        // Link to doc
        $url = $this->addElement('file', 'url', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'label'  => 'Url:',
            'decorators' => array('File'),
        ));

		// Used to set an error if one occurs
        $this->addElement('hidden', 'err', array(
            'required' => false,
        ));
        
		// Submit button
        $this->addElement('submit', 'submit', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
            'decorators' => array('ViewHelper'),
            'label'      => 'Upload',
            'ignore'     => true
        ));


    }
}
