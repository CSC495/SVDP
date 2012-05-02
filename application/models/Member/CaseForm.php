<?php
class Application_Model_Member_CaseForm extends Zend_Form
{

	public function __construct($options = null){
		parent::__construct($options);
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/member/case'));
		
		$this->setDecorators(array(
				array('ViewScript', array('viewScript' => 'member/caseViewScript.phtml'))
		));
		
		$clientID = $this->addElement('text', 'clientID',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Client #:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 30,
		));
		
		$name = $this->addElement('text', 'name',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Client Name:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 30,
		));
		
		$homePhone = $this->addElement('text', 'homePhone',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => true,
				'label'      => 'Home Phone:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 13,
		));
		
		$caseID = $this->addElement('text', 'caseID',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Case #:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 30,
		));
		
		$status = $this->addElement('text', 'status',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(10)),
				),
				'required'   => true,
				'label'      => 'Status:',
				'size'		 => 10,
		));
		
		$visitDate = $this->addElement('text', 'visitDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => true,
				'label'      => 'Visit Date (YYYYMMDD):',
				'size'		 => 30,
		));
		
		$miles = $this->addElement('text', 'miles',array(
				'validators' => array(
						'Digits',
						array('StringLength', false, array(11)),
				),
				'required'   => false,
				'label'      => 'Miles Traveled:',
				'size'		 => 11,
		));
		
		$hours = $this->addElement('text', 'hours',array(
				'validators' => array(
						'Digits',
						array('StringLength', false, array(11)),
				),
				'required'   => true,
				'label'      => 'Hours Spent:',
				'size'		 => 11,
		));
		
		$caseNeed = $this->addElement('text', 'caseNeed',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Case Need:',
				'size'		 => 30,
		));
		
		$amount = $this->addElement('text', 'amount',array(
				'filters'    => array('StringTrim',
				array('LocalizedToNormalized', false, array('precision', 2))),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 7)),
				),
				'required'   => true,
				'label'      => 'Amount Needed:',
				'size'		 => 7,
		));
		
		$openedUserID = $this->addElement('text', 'openedUserID',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Opened User:',
				'size'		 => 30,
		));
		
		$openedDate = $this->addElement('text', 'openedDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
				),
				'required'   => true,
				'label'      => 'Opened Date:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 8,
		));
		
		$referral = $this->addElement('text', 'referral',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 256)),
				),
				'required'   => false,
				'label'      => 'Referred To:',
				'size'		 => 256,
		));
		
		$referredReason = $this->addElement('text', 'referredReason',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
				),
				'required'   => true,
				'label'      => 'Reason For Referral:',
				'size'		 => 30,
		));
		
		$referralDate = $this->addElement('text', 'referralDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
						array('Date', false, array('format', 'yyyymmdd')),
				),
				'required'   => true,
				'label'      => 'Referral Date (YYYYMMDD):',
				'size'		 => 8,
		));
		
		$commentDate = $this->addElement('text', 'commentDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(8)),
				),
				'required'   => true,
				'label'      => 'Comment Date:',
				'attribs'    => array('disabled' => 'disabled'),
				'size'		 => 8,
		));
		
		$comment = $this->addElement('text', 'comment',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
				),
				'required'   => true,
				'label'      => 'Comment:',
				
		));
		
		////////////Edit Client Button/////////////////
		$editCase = $this->addElement('submit', 'editCase', array(
				'required' => false,
				'ignore'   => true,
				'label'    => '     Edit Case     ',
				'class'    => 'btn-success',
		));
		////////////End Edit Client Button/////////////////
	}
}
