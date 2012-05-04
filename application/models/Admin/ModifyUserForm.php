<?php
class Application_Model_Admin_ModifyUserForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('modify');
		$this->setAttrib('id', 'modify');
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/admin/modifyproc'));
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'admin/modifyViewScript.phtml'))
		));
                
		// Read only user id
		$userid = $this->addElement('text', 'userid', array(
			'required'   => true,
			'label'      => 'User Id:',
			'readonly'   => true,
		));
		
		// Read only user first name
               $firstname = $this->addElement('text', 'firstname', array(
                   'required'   => true,
                   'label'      => 'First Name:',
		   'readonly'   => true,
               ));
	       
               // Read only user last name
               $lastname = $this->addElement('text', 'lastname', array(
                   'required'   => true,
                   'label'      => 'Last Name:',
		   'readonly'   => true,
               ));
        
		// User e-mail
               $email = $this->addElement('text', 'email', array(
			'validators' => array('Email'),
			'required'   => true,
			'label'      => 'E-Mail:',
               ));
               
               // User cell phone
               $cell = $this->addElement('text', 'cell', array(
                   'required'   => true,
                   'label'      => 'Cell Phone:',
               ));
               
               // User home phone
               $home = $this->addElement('text', 'home', array(
                   'required'   => true,
                   'label'      => 'Home Phone:',
               ));
               
               // Users role
               $role = $this->addElement('select','role',array(
			'label' => 'Role:',
			'multiOptions' => array ( App_Roles::MEMBER      => 'Member',
						  App_Roles::TREASURER   => 'Treasurer',
						  App_Roles::ADMIN       => 'Admin',)
			,));
               
               // Users status
               $status = $this->addElement('select','status',array(
			'label' => 'Status:',
			'multiOptions' => array ( '1'   => 'Active',
						  '0'   => 'Inactive',)
			,
			'class'      => 'input-medium',));
               
               $submit = $this->addElement('submit', 'submit', array(
		   'filters'    => array('Digits'),
		   'validators' => array('Digits'),
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Submit Changes',
		   'class'    => 'btn',
                ));
	}
}
