<?php
class Application_Model_Login_ForgotForm extends Twitter_BootStrap_Form_Vertical
{
	public function __construct($options = null){
		
		parent::__construct($options);
		
		$this->addElementPrefixPath(
			'Twitter_Bootstrap_Form_Decorator',
			'Twitter/Bootstrap/Form/Decorator',
			'decorator'
		);
		
		$this->setName('forgot');
		$this->setAttrib('id', 'forgot');
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/forgot'));
		
		$this->addElement('text','username',array(
			'required'   => true,
			'filters'    => array('stringTrim'),
			'label'      => 'Username:',
			'validators' => array(
				array('NotEmpty', true, array(
					'type' => 'string',
					'messages' => array('isEmpty' => 'Field is required.')))),
                ));

		$this->addDisplayGroup(array('username'), 'id', null);

		$this->addElement('submit','submit', array(
			'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
			'label'       => 'Send E-Mail',
			'decorators' => array('ViewHelper'),
		));
	}
}