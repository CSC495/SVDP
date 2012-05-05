<?php
//class Application_Model_Login_ForgotForm extends Twitter_BootStrap_Form_Vertical
//{
//	public function __construct($options = null){
//		
//		parent::__construct($options);
//		$this->setName('forgot');
//		$this->setAttrib('id', 'forgot');
//		$this->setMethod('post');
//		$this->setDecorators(array('FormElements'));
//
//		$baseUrl = new Zend_View_Helper_BaseUrl();
//		$this->setAction($baseUrl->baseUrl('/login/forgotprocess'));
//		
//		$this->addElement('text','username',array(
//			'required'   => true,
//			'filters'    => array('stringTrim'),
//			'label'      => 'Username:',
//		));
//		
//		$this->addDisplayGroup(array('username'), 'id', null);
//
//		$this->addElement('submit','submit', array(
//			'buttonType'  => Twitter_Bootstrap_Form_Element_Submit::BUTTON_SUCCESS,
//			'label'       => 'Send E-Mail',
//			'decorators' => array('ViewHelper')
//		));
//	}
class Application_Model_Login_ForgotForm extends Zend_Form
{
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('forgot');
		$this->setAttrib('id', 'forgot');
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/forgotprocess'));
		

		$username = $this->addElement('text', 'username', array(
			'required'   => true,
		        'filters'   => array('stringTrim'),
			'label'      => 'Username:',
		));

                $login = $this->addElement('submit', 'submit', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'E-Mail Password',
		   'class'    => 'btn btn-success',
                ));

	}
}
