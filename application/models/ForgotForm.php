<?php
class Application_Model_ForgotForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('forgot');
		$this->setAttrib('id', 'forgot');
		$this->setMethod('post');

        $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/forgot'));
		
		// Username must consist of letters only
		//          must be between 5 and 20 characters
		$username = $this->addElement('text', 'username', array(
                                   'filters'    => array('StringTrim', 'StringToLower'),
                                  'validators' => array(
				          'Alnum',
                                  array('StringLength', false, array(1, 20)),
		                        ),
				   'required'   => true,
				   'label'      => 'Username:',
				 ));

               $login = $this->addElement('submit', 'proceed', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'E-mail Password',
                ));
               
	}
}
