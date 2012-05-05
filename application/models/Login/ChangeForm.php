<?php
class Application_Model_Login_ChangeForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('change');
		$this->setAttrib('id', 'change');
		$this->setMethod('post');

        $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/processchange'));
		
		// Password must be minimum of 8 characters and contain 1 digit
		$password = $this->addElement('text', 'password', array(
				'validators' => array( new App_Validate_Password() ),
				'required'   => true,
				'filters'   => array('stringTrim'),
				'label'      => 'New Password:',
		));
	
		// verify the users input
		$verify = $this->addElement('text', 'verify',array(
				'validators' => array( new App_Validate_Password() ),
				'required'   => true,
				'filters'   => array('stringTrim'),
				'label'      => 'Verify Password:',
		));
		
                $submit = $this->addElement('submit', 'submit', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Submit',
                   'class'    => 'btn-success btn'
                ));
               
	}
}
