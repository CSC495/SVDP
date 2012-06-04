<?php
/**
 * Class implements the form to change a password
 */
class Application_Model_Login_ChangeForm extends Twitter_Bootstrap_Form_Vertical
{
	/**
	 * Default constructor to build form
	 *
	 * @param array $options Used to build super()
	 *
	 * @return null
	 */
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('change');
		$this->setAttrib('id', 'change');
		$this->setMethod('post');

                $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/change'));
				
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
		
		// Password must be minimum of 8 characters and contain 1 digit
		$password = $this->addElement('password', 'password', array(
				'validators' => array( new App_Validate_Password() ),
				'required'   => true,
				'filters'   => array('stringTrim'),
				'label'      => 'New Password:',
				'name'       => 'password',
				'id'         => 'password',
		));
	
		// verify the users input
		$verify = $this->addElement('password', 'verify',array(
				'validators' => array( new App_Validate_Password() ),
				'required'   => true,
				'filters'   => array('stringTrim'),
				'label'      => 'Verify Password:',
				'name'       => 'verify',
				'id'         => 'verify',
		));
		// Used to display error if any occurs
		$err = $this->addElement('hidden','err',array(
			'required' => false,
			'name'     => 'err',
			'id'       => 'err',
		));
		
		$submit = $this->addElement('submit', 'submit', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Submit',
                   'class'    => 'btn-success btn',
				   'decorators' => array('ViewHelper')
                ));
               
	}
}
