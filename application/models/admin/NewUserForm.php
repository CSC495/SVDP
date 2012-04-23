<?php
class Application_Model_Admin_NewUserForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('new');
		$this->setAttrib('id', 'new');
		$this->setMethod('post');
		$this->setAction('/SVDP/public/admin/newmember');
		
		// The memebrs name
		$name = $this->addElement('text', 'name', array(
                                   'filters'    => array('StringTrim', 'StringToLower'),
				   'required'   => true,
				   'label'      => 'Name:',
				 ));
		
               // Members phone number
               $phone = $this->addElement('text', 'phone', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Phone Number:',
               ));
        
		// IMemebers e-mail
               $email = $this->addElement('text', 'email', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Email:',
               ));
               
	       // Type of memebr
               $email = $this->addElement('text', 'type', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Member Type:',
               ));
               
               $adjust = $this->addElement('submit', 'submit', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Add New Contact',
                ));
               
	       //$jsparam = 'javascript:return adjust_validation(this)';
	       //$this->addAttribs(array('onSubmit'=>$jsparam));
	}
}
