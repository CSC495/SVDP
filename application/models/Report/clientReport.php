<?php
class Application_Model_Report_clientReport extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('login');
		$this->setAttrib('id', 'login');
		$this->setMethod('post');
		$this->setAction('');
		
		// Username must consist of letters only
		//          must be between 1 and 20 characters
		$firstName = $this->addElement('text', 'firstName', array(
                                   'filters'    => array('StringTrim', 'StringToLower'),
                                  'validators' => array(
				          'Alnum',
                                  array('StringLength', false, array(1, 20)),
		                        ),
				   'required'   => true,
				   'label'      => 'First Name:',
				 ));
		
               // lastname must consist of alphanumeric characters only
               //          must be between 1 and 20 characters
               $lastName = $this->addElement('text', 'lastName', array(
                   'filters'    => array('StringTrim'),
                   'validators' => array(
                       'Alnum',
                       array('StringLength', false, array(1, 20)),
                   ),
                   'required'   => true,
                   'label'      => 'Last Name:',
               ));
               // lastname must consist of alphanumeric characters only
               //          must be between 1 and 13 characters
               $phoneNum = $this->addElement('text', 'phoneNum', array(
                   'filters'    => array('StringTrim'),
                   'validators' => array(
                       'Alnum',
                       array('StringLength', false, array(6, 20)),
                   ),
                   'required'   => true,
                   'label'      => 'Phone Number:',
               ));
        
		
               $login = $this->addElement('submit', 'create', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Create Report',
               ));           
	}
}
