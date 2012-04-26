<?php
class Application_Model_Login_LoginForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('login');
		$this->setAttrib('id', 'login');
		$this->setMethod('post');

        $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/process'));
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'login/loginViewScript.phtml'))
		));	
		
		// User Name
		$username = $this->addElement('text', 'username', array(
				   'required'   => true,
				   'label'      => 'Username:',
				 ));
		
               // Password must consist of alphanumeric characters only
               //          must be between 6 and 20 characters
               $password = $this->addElement('password', 'password', array(
                   'required'   => true,
                   'label'      => 'Password:',
               ));
        
		
               $login = $this->addElement('submit', 'login', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => '        Login        ',
		   'class'    => 'btn-success',
                ));
               
                $forgot = $this->addElement('submit','forgot', array(
                    'required' => false,
                    'ignore' => true,
                    'label' => 'Forgot Password',
		    'class' => 'btn-info',
                ));
	}
}
