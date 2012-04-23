<?php
class Application_Model_CaseForm extends Zend_Form
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
		));
		
		$status = $this->addElement('text', 'status',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alpha',
						array('StringLength', false, array(10)),
				),
				'required'   => true,
				'label'      => 'Status:',
		));
		
		$visitDate = $this->addElement('text', 'visitDate',array(
				'filters'    => array('Digits'),
				'validators' => array(
						'Digits',
						array('StringLength', false, array(13)),
				),
				'required'   => true,
				'label'      => 'Visit Date (YYYYMMDD):',
		));
		
		$miles = $this->addElement('text', 'miles',array(
				'validators' => array(
						'Digits',
						array('StringLength', false, array(11)),
				),
				'required'   => false,
				'label'      => 'Miles Traveled:',
		));
		
		$hours = $this->addElement('text', 'hours',array(
				'validators' => array(
						'Digits',
						array('StringLength', false, array(11)),
				),
				'required'   => true,
				'label'      => 'Hours Spent:',
		));
		
		$caseNeed = $this->addElement('text', 'caseNeed',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Case Need:',
		));
		
		$amount = $this->addElement('text', 'amount',array(
				'filters'    => array('StringTrim',
				array('LocalizedToNormalized', false, array('precision', 2))),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 50)),
				),
				'required'   => true,
				'label'      => 'Amount Needed:',
		));
		
		$openedUserID = $this->addElement('text', 'openedUserID',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 30)),
				),
				'required'   => true,
				'label'      => 'Opened User:',
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
		));
		
		$referral = $this->addElement('text', 'referral',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 256)),
				),
				'required'   => false,
				'label'      => 'Referred To:',
		));
		
		$referredReason = $this->addElement('text', 'referredReason',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
				),
				'required'   => true,
				'label'      => 'Reason For Referral:',
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
		));
		
		$comment = $this->addElement('text', 'comment',array(
				'filters'    => array('StringTrim', 'StringToLower'),
				'validators' => array(
						'Alnum',
				),
				'required'   => true,
				'label'      => 'Comment:',
		));
	}
}