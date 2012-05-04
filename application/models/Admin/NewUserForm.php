<?php
class Application_Model_Admin_NewUserForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('new');
		$this->setAttrib('id', 'new');
		$this->setMethod('post');

        $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/admin/newmember'));
		$this->setDecorators(array(
			array('ViewScript',array('viewScript' => 'admin/newViewScript.phtml'))
		));
		
		// The memebrs name
		$firstname = $this->addElement('text', 'firstname', array(
                                   'filters'    => array('StringTrim', 'StringToLower'),
				   'required'   => true,
				   'label'      => 'First Name:',
				 ));
		
		// The memebrs name
		$lastname = $this->addElement('text', 'lastname', array(
                                   'filters'    => array('StringTrim', 'StringToLower'),
				   'required'   => true,
				   'label'      => 'Last Name:',
				 ));
		
		// Members phone type
		$phonetype = $this->addElement('select','phonetype',array(
			'label' => 'Primary Phone Type:',
			'multiOptions' => array ( 'home'    => 'Home',
						  'cell'     => 'Cell',
						  'work' => 'Work',)
			,));
		
                // Members phone number
                $phone = $this->addElement('text', 'phone', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Primary Phone Number:',
                ));
        
		// Memebrs other phone
		$otherphonetype = $this->addElement('select','otherphonetype',array(
			'label' => 'Other Phone Type:',
			'multiOptions' => array ( 'home'    => 'Home',
						  'cell'     => 'Cell',
						  'work' => 'Work',)
			,));
		
		// Members other phone
		$otherphone = $this->addElement('text', 'otherphone', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Other Phone Number:',
                ));
		
		// IMemebers e-mail
                $email = $this->addElement('text', 'email', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Email:',
                ));
               
	        // Type of memebr
                $type = $this->addElement('select','type',array(
			'label' => 'Member Type:',
			'multiOptions' => array ( App_Roles::MEMBER    => 'Member',
						  App_Roles::ADMIN     => 'Admin',
						  App_Roles::TREASURER => 'Treasurer',)
			,));
               
                $adjust = $this->addElement('submit', 'submit', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Add New Contact',
		   'class'    => 'btn-success btn',
                ));
               
	}
}
