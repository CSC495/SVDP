<?php
class Application_Model_Login_ChangeForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('change');
		$this->setAttrib('id', 'change');
		$this->setMethod('post');

        $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/processpwd'));
		
		// Username must consist of letters only
		//          must be between 5 and 20 characters
		$password = $this->addElement('text', 'password', array(
				   'required'   => true,
				   'label'      => 'New Password:',
				 ));
    
               $submit = $this->addElement('submit', 'submit', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Submit',
                   'class'    => 'btn-inverse'
                ));
               
	}
}
