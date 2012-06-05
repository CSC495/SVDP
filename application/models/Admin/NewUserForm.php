<?php
/**
 * Class represents the form which is used to create a new user
 */
class Application_Model_Admin_NewUserForm extends Twitter_Bootstrap_Form_Horizontal
{
	/**
	 * Default constructor for user form
	 *
	 * @param array $options Options to be passed to Super() constructor
	 *
	 * @return null
	 */
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('new');
		$this->setAttrib('id', 'new');
		$this->setMethod('post');
		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/admin/new'));
		$this->setDecorators(array(
			array('ViewScript',array('viewScript' => 'admin/newViewScript.phtml'))
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
		
		// The memebers name
		$firstname = $this->addElement('text', 'firstname', array(
                                   'filters'    => array('StringTrim'),
				   'required'   => true,
				   'label'      => 'First Name:',
				 ));
		
		// The memebrs name
		$lastname = $this->addElement('text', 'lastname', array(
                                   'filters'    => array('StringTrim'),
				   'required'   => true,
				   'label'      => 'Last Name:',
				 ));
		
		// Members phone number
		$home = $this->addElement('text', 'home', array(
					'filters'    => array('StringTrim','Digits'),
					'required'   => false,
					'class'      => 'phone requireone',
					'label'      => 'Home Phone:',
					'validators' => array(
					array('StringLength', true, array(
						'min' => 10,
						'max' => 10,
						'messages' => array(
						'stringLengthTooShort' => 'Phone number must be 10 digits.',
						'stringLengthTooLong' => 'Phone number must be 10 digits.',
							)))),
		));
        
		// Members other phone
		$cell = $this->addElement('text', 'cell', array(
                   'filters'    => array('StringTrim','Digits'),
                   'required'   => false,
		   'class'      => 'phone requireone',
                   'label'      => 'Cell Phone:',
				   'validators' => array(
					array('StringLength', true, array(
						'min' => 10,
						'max' => 10,
						'messages' => array(
						'stringLengthTooShort' => 'Phone number must be 10 digits.',
						'stringLengthTooLong' => 'Phone number must be 10 digits.',
							)))),
                ));
		
		// Memebers e-mail
		$email = $this->addElement('text', 'email', array(
					'filters'    => array('StringTrim'),
					'validators' => array('EmailAddress'),
					'required'   => true,
					'label'      => 'Email:',
                ));
               
		// Type of memeber
		$role = $this->addElement('select','role',array(
					'label' => 'User Type:',
					'value' => App_Roles::MEMBER,
					'multiOptions' => array ( 'M'   => 'Member',
								  App_Roles::ADMIN     => 'Admin',
								  App_Roles::TREASURER => 'Treasurer',),
					));
               
		$adjust = $this->addElement('submit', 'submit', array(
					'required' => false,
                    'ignore'   => true,
                    'label'    => 'Add New User',
                    'class'    => 'btn btn-success',
					'decorators' => array('ViewHelper'),
                ));
               
	}
}
