<?php
class Application_Model_ClientForm extends Zend_Form
{

	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('member');
		$this->setAttrib('id', 'member');
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/member/client'));
		
		$clientID = $this->addElement('text', 'clientID',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Client #:',
				'attribs'    => array('disabled' => 'disabled'),
				));
		
		$firstName = $this->addElement('text', 'firstName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
						),
				'required'   => true,
				'label'      => 'First Name:',
				));
		$lastName = $this->addElement('text', 'lastName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Last Name:',
				));
		
		$otherName = $this->addElement('text', 'otherName',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Other Name:',
				));
		
		$doNotHelp = $this->addElement('checkbox', 'doNotHelp',array(
				'required'   => false,
				'label'      => 'Do NOT Help:',
		));
		
		$cellPhone = $this->addElement('text', 'cellPhone',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => false,
				'label'      => 'Cell Phone:',
		));
		
		$homePhone = $this->addElement('text', 'homePhone',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => true,
				'label'      => 'Last Name:',
		));
		
		$workPhone = $this->addElement('text', 'workPhone',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => false,
				'label'      => 'Last Name:',
		));
		
		$address = $this->addElement('text', 'address',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Address:',
		));
		
		$city = $this->addElement('text', 'city',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'City:',
		));
		
		$state = $this->addElement('text', 'state',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'State:',
		));
		
		$zipcode = $this->addElement('text', 'zipcode',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Zip Code:',
		));
		
		$marriageStatus = $this->addElement('checkbox', 'marriageStatus',array(
				'required'   => false,
				'label'      => 'Marital Status:',
				));
		
		$spouse = $this->addElement('text', 'spouse',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => false,
				'label'      => 'Spouse\'s Name:',
		));
		
		$birthdate = $this->addElement('text', 'birthdate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						Date(array('format' => 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Birthdate (YYYYMMDD):',
				));
		
		$ssn4 = $this->addElement('text', 'ssn4',array(
				'validators' => array(
						'Digits',
						array('StringLength', false, array(4)),
				),
				'required'   => true,
				'label'      => 'Last 4 of SSN:',
				));
		
		$veteranFlag = $this->addElement('checkbox', 'veteranFlag',array(
				'required'   => false,
				'label'      => 'Veteran:',
		));
		
		$resideParish = $this->addElement('text', 'resideParish',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 50)),
				),
				'required'   => true,
				'label'      => 'Parish Name:',
		));
		
		$memberParish = $this->addElement('text', 'memberParish',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 50)),
				),
				'required'   => true,
				'label'      => 'Parishioner at:',
		));
		
		$employer = $this->addElement('text', 'employer',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 50)),
				),
				'required'   => true,
				'label'      => 'Employer:',
		));
		
		$position = $this->addElement('text', 'position',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 50)),
				),
				'required'   => true,
				'label'      => 'Position:',
		));
		
		$startDate = $this->addElement('text', 'startDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						Date(array('format' => 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Start Date:',
		));
		
		$endDate = $this->addElement('text', 'endDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						Date(array('format' => 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'End Date:',
		));
		
		$createdDate = $this->addElement('text', 'createdDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						Date(array('format' => 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Date Created:',
				'attribs'    => array('disabled' => 'disabled'),
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
		));

	}
}