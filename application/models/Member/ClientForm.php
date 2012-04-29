<?php
class Application_Model_Member_ClientForm extends Zend_Form
{

	const TYPE_NULL  = 'NULL';
	const TYPE_UPDATE = 'Address Update';
	const TYPE_MOVED  = 'Moved';
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/member/client'));
			
		$this->setDecorators(array(
				array('ViewScript', array('viewScript' => 'member/clientViewScript.phtml'))
		));
		
		////////////Client ID/////////////////
		$clientID = $this->addElement('text', 'clientID',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Client #:',
				'size'		 => 30,
				'attribs'    => array('disabled' => 'disabled'),
				));
		////////////End Client ID/////////////////
		
		////////////Personal Information/////////////////
		$firstName = $this->addElement('text', 'firstName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
						),
				'required'   => true,
				'label'      => 'First Name:',
				'size'		 => 30,
				));
		
		$lastName = $this->addElement('text', 'lastName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Last Name:',
				'size'		 => 30,
				));
		
		$otherName = $this->addElement('text', 'otherName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Other Name:',
				'size'		 => 30,
				));
		
		$doNotHelp = $this->addElement('checkbox', 'doNotHelp',array(
				'required'   => false,
				'label'      => 'Do NOT Help:',
		));
		
		$doNotHelpReason = $this->addElement('text', 'doNotHelpReason',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Reason For Not Helping:',
				'size'		 => 30,
		));
		
		$birthdate = $this->addElement('text', 'birthdate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						array('Date', false, array('format', 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Birthdate (YYYYMMDD):',
				'size'		 => 8,
		));
		
		$ssn4 = $this->addElement('text', 'ssn4',array(
				'validators' => array(
						'Digits',
						array('StringLength', false, array(4)),
				),
				'required'   => true,
				'label'      => 'Last 4 of SSN:',
				'size'		 => 4,
		));
		
		$veteranFlag = $this->addElement('checkbox', 'veteranFlag',array(
				'required'   => false,
				'label'      => 'Veteran:',
		));
		
		$homePhone = $this->addElement('text', 'homePhone',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => true,
				'label'      => 'Home Phone:',
				'size'		 => 13,
		));
		
		$cellPhone = $this->addElement('text', 'cellPhone',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => false,
				'label'      => 'Cell Phone:',
				'size'		 => 13,
		));
		
		$workPhone = $this->addElement('text', 'workPhone',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => false,
				'label'      => 'Work Phone:',
				'size'		 => 13,
		));
		////////////End Personal Information/////////////////
		
		////////////Adress Information/////////////////
		$addressChange = $this->addElement('select', 'addressChange', array(
				'label'			=> 'Address Change',
				'multiOptions'	=> array(
						'NULL',
						'Address Update',
						'Moved')
				));
		
		$street = $this->addElement('text', 'street',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Street:',
				'size'		 => 100,
		));
		
		$apartment = $this->addElement('text', 'apartment',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Apt #:',
				'size'		 => 30,
		));
		
		$city = $this->addElement('text', 'city',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'City:',
				'size'		 => 50,
		));
		
		$state = $this->addElement('text', 'state',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'State:',
				'size'		 => 2,
		));
		
		$zipcode = $this->addElement('text', 'zipcode',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Zip Code:',
				'size'		 => 5,
		));
		////////////Address Information/////////////////
		
		////////////Spouse Information/////////////////
		$marriageStatus = $this->addElement('select', 'marriageStatus', array(
				'label'			=> 'Marriage Status:',
				'multiOptions'	=> array(
						'Single',
						'Married',
						'Divorced',
						'Seperated',
						'Other')
		));
		
		$spouseFirstName = $this->addElement('text', 'spouseFirstName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Spouse\'s First Name:',
				'size'		 => 30,
		));
		
		$spouseLastName = $this->addElement('text', 'spouseLastName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Spouse\'s Last Name:',
				'size'		 => 30,
		));
		////////////End Spouse Information/////////////////
		
		////////////Household Members/////////////////
		$houseMemberFirst = $this->addElement('text', 'houseMemberFirst',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Household Member First Name:',
				'size'		 => 30,
		));
		
		$houseMemberLast = $this->addElement('text', 'houseMemberLast',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Household Member Last Name:',
				'size'		 => 30,
		));
		
		$relationship = $this->addElement('text', 'relationship',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Relationship:',
				'size'		 => 30,
		));		
		
		$houseMemberBirthdate = $this->addElement('text', 'houseMemberBirthdate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						array('Date', false, array('format', 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Birthdate (YYYYMMDD):',
				'size'		 => 8,
		));
		
		$dateLeft = $this->addElement('text', 'dateLeft',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						array('Date', false, array('format', 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Left Household (YYYYMMDD):',
				'size'		 => 8,
		));
		////////////End Household Members/////////////////
		
		////////////Parish Info/////////////////		
		$resideParish = $this->addElement('select', 'resideParish', array(
				'label'			=> 'Parish Name:',
				'multiOptions'	=> array(
						'St. Raphael',
						'Holy Spirit',
						'St. Elizabeth Seton',
						'St. Thomas',
						'SS. Peter & Paul',
						'Other')
		));
		
		$memberParish = $this->addElement('select', 'memberParish', array(
				'label'			=> 'Parish Name:',
				'multiOptions'	=> array(
						'St. Raphael',
						'Holy Spirit',
						'St. Elizabeth Seton',
						'St. Thomas',
						'SS. Peter & Paul',
						'Other',
						'None')
		));
		////////////End Parish Info/////////////////
		
		////////////Employer/////////////////
		$employer = $this->addElement('text', 'employer',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 50)),
				),
				'required'   => true,
				'label'      => 'Employer:',
				'size'		 => 50,
		));
		
		$position = $this->addElement('text', 'position',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 50)),
				),
				'required'   => true,
				'label'      => 'Position:',
				'size'		 => 50,
		));
		
		$startDate = $this->addElement('text', 'startDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						array('Date', false, array('format', 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Start Date:',
				'size'		 => 8,
		));
		
		$endDate = $this->addElement('text', 'endDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						array('Date', false, array('format', 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'End Date:',
				'size'		 => 8,
		));
		////////////End Employer/////////////////
		
		////////////Record Information/////////////////
		$createdDate = $this->addElement('text', 'createdDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						array('Date', false, array('format', 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Date Created:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 8,
				));
		
		$created_user = $this->addElement('text', 'createdUser',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Added By:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 8,
		));
		////////////End Record Info/////////////////
		
		////////////Edit Client Button/////////////////
		$editClient = $this->addElement('submit', 'editClient', array(
				'required' => false,
				'ignore'   => true,
				'label'    => '     Edit Client     ',
				'class'    => 'btn-success',
		));
		////////////End Edit Client Button/////////////////

	}
}
