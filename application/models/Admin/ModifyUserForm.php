<?php
/**
 * Class represents a form which is used to modify a particular users data
 */
class Application_Model_Admin_ModifyUserForm extends Twitter_Bootstrap_Form_Vertical
{
	/**
	 * Default constructor to the form
	 *
	 * @return null
	 */
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('modify');
		$this->setAttrib('id', 'modify');
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/admin/modify'));
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'admin/modifyViewScript.phtml'))
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
                
		// Read only user id
		$userid = $this->addElement('text', 'userid', array(
			'required'   => true,
			'label'      => 'User Id:',
			'readonly'   => true,
		));
		
		// Read only user first name
	    $firstname = $this->addElement('text', 'firstname', array(
			'filters'    => array('StringTrim'),
			'required'   => true,
			'label'      => 'First Name:',
			'readonly'   => true,
               ));
	       
	    $lastname = $this->addElement('text', 'lastname', array(
			'filters'    => array('StringTrim'),
			'required'   => true,
			'label'      => 'Last Name:',
	    ));
        
		// User e-mail
	    $email = $this->addElement('text', 'email', array(
			'filters'    => array('StringTrim'),
			'validators' => array('EmailAddress'),
			'required'   => true,
			'label'      => 'Email:',
	    ));
               
	    // User cell phone
	    $cell = $this->addElement('text', 'cell', array(
		    'required'   => false,
		    'filters'    => array('Digits'),
		    'label'      => 'Cell Phone:',
		    'class'      => 'phone',
		    'validators' => array(
			array('StringLength', true, array(
				'min' => 10,
				'max' => 10,
				'messages' => array(
				'stringLengthTooShort' => 'Phone number must be 10 digits.',
				'stringLengthTooLong' => 'Phone number must be 10 digits.',
                    )))),
               ));
               
	    // User home phone
	    $home = $this->addElement('text', 'home', array(
			'required'   => false,
		    'filters'    => array('Digits'),
		    'label'      => 'Home Phone:',
		    'class'      => 'phone',
		    'validators' => array(
			array('StringLength', true, array(
				'min' => 10,
				'max' => 10,
				'messages' => array(
				'stringLengthTooShort' => 'Phone number must be 10 digits.',
				'stringLengthTooLong' => 'Phone number must be 10 digits.',
                    )))),
               ));
               
	    // Users role
	    $role = $this->addElement('select','role',array(
			'label' => 'Role:',
			'multiOptions' => array ( App_Roles::MEMBER      => 'Member',
						  App_Roles::TREASURER   => 'Treasurer',
						  App_Roles::ADMIN       => 'Admin',)
			,));
	    // Used to indicate errors on role
	    $roleErr = $this->addElement('hidden','roleErr', array(
			'ignore'   => true,
			'required' => false,
			));
               
	    // Users status
	    $status = $this->addElement('select','status',array(
			'label' => 'Status:',
			'multiOptions' => array ( '1'   => 'Active',
									  '0'   => 'Inactive',),
			'class'      => 'input-medium',));
               
	    $submit = $this->addElement('submit', 'submit', array(
		    'required' => false,
		    'ignore'   => true,
		    'label'    => 'Submit',
		    'class'    => 'btn btn-success',
		    'decorators' => array('ViewHelper'),
		));
	}
}
