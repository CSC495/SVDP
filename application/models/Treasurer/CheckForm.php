<?php

class Application_Model_Treasurer_CheckForm extends Twitter_Bootstrap_Form_Horizontal 
{

    public function __construct($check)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
		
		
        parent::__construct(array(
            'action' => $baseUrl->baseUrl(App_Resources::TREASURER) . '/checkReq',
            'method' => 'post',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array('viewScript' => 'treasurer/checkViewScript.phtml')),
                'Form',
            ),
        ));
		
		$service = new App_Service_Member();
		$case = $service->getCaseById($check->getCase());
		
		
		$this->addElement('text', 'checkID',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check ID',
				'size'		 => 7,
		));
		$this->checkID->setValue($check->getID());
		
		
		$this->addElement('text', 'SVDPname',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Submitted By',
				'size'		 => 7,
		));
		$this->SVDPname->setValue($check->getUserFName() . ' ' . $check->getUserLName());
		
		
		$this->addElement('text', 'clientName',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Client Name',
				'size'		 => 7,
		));
		$this->clientName->setValue($case->getClient()->getFullName());
		
		
		$this->addElement('text', 'contactFirst',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Contact First Name',
				'size'		 => 7,
		));
		$this->contactFirst->setValue($check->getContactFirstName());
		
		$this->addElement('text', 'contactLast',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Contact Last Name',
				'size'		 => 7,
		));
		$this->contactLast->setValue($check->getContactLastName());
		
		
		$this->addElement('text', 'contactPhone',  array(
				'filters'   =>	array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'  => 	true,
				'required'  => 	true,
				'label'     => 	'Contact Phone #',
				'size'		=> 	7,
				'class' 	=> 	'phone'
		));
		$this->contactPhone->setValue($check->getPhone());
		
		
		
		$this->addElement('text', 'amount',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Amount',
				'size'		 => 7,
				'class'		 => 'money'
		));
		$this->amount->setValue(App_Formatting::formatCurrency($check->getAmount()));
		
		
		$this->addElement('text', 'caseID',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Case ID',
				'size'		 => 7,
		));
		$this->caseID->setValue($check->getCase());
		
		
		$this->addElement('text', 'requestDate',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Date'),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Requested',
				'value' 	 => '',
				'size'		 => 10,
		));
		$this->requestDate->setValue(App_Formatting::formatDate($check->getRequestDate()));
		
		
		$this->addElement('text', 'checkNum',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Real Check Number',
				'size'		 => 7,
				'class'		 => 'number'
		));
		$this->checkNum->setValue($check->getCheckNumber());
		
		
		$this->addElement('text', 'issueDate',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Issued Date',
				'size'		 => 7,
		));
		$this->issueDate->setValue(App_Formatting::formatDate($check->getIssueDate()));
		
		
		$this->addElement('text', 'payeeName',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Payee Name',
				'size'		 => 7,
		));
		$this->payeeName->setValue($check->getPayeeName());
		
		
		$addr = $check->getAddress();
		$this->addElement('textarea', 'address', array(
                'label' => 'Payee Address',
                'required' => true,
                'filters' => array('StringTrim'),
                'dimension' => 3,
                'rows' => 3,
				'readonly' => true,
            ));
		$this->address->setValue($addr->getStreet() . " " . $addr->getApt() . "\n" . 
									$addr->getCity() . ", " . $addr->getState() . " " . $addr->getZip());
		
		
		$this->addElement('text', 'payeeAccount',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Payee Account #',
				'size'		 => 7,
		));
		$this->payeeAccount->setValue($check->getAccountNumber());
		
		
		$this->addElement('text', 'caseNeed',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Case Need',
				'size'		 => 7,
		));
		$this->caseNeed->setValue($check->getCaseNeedName());
		
		
		
		$this->addElement('textarea', 'commentText', array(
			'label' => 'Comments',
			'required' => true,
			'filters' => array('StringTrim'),
			'validators' => array(
				array('NotEmpty', true, array(
					'type' => 'string',
					'messages' => array('isEmpty' => 'You must enter a comment.'),
				)),
			),
			'dimension' => 3,
			'rows' => 3,
			'readonly' => true,
		));
		$this->commentText->setValue($check->getComment());

		
		
		if($check->getStatus() === 'P'){
			$this->addElement('submit', 'issueCheck', array(
				'label' => 'Issue Check Request',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-success',
			));
			
			$this->addElement('submit', 'denyCheck', array(
				'label' => 'Deny Check Request',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-danger',
			));
			
			$this->addElement('submit', 'editCheck', array(
				'label' => 'Edit Check Request',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-info',
			));
			
			$this->addElement('submit', 'addComment', array(
				'label' => 'Add A Comment',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-info',
			));
			
			if($check->getCheckNumber() == null){
				$this->checkNum->setAttrib('readonly', null);
			}
		}
    }



    public function editCheckReq($t, $chk)
    {
		
		if($t === 'Edit Check Request'){
			$this->payeeName->setAttrib('readonly', null);
			$this->payeeAccount->setAttrib('readonly', null);
			$this->contactFirst->setAttrib('readonly', null);
			$this->contactLast->setAttrib('readonly', null);
			$this->contactPhone->setAttrib('readonly', null);
			
			$this->checkNum->setAttrib('readonly', null);
			$this->commentText->setAttrib('readonly', null);
			
			$this->issueCheck->setAttrib('disabled', true);
			$this->denyCheck->setAttrib('disabled', true);
			$this->addComment->setAttrib('disabled', true);
			
			$this->editCheck->setLabel('Submit Edits');
		}
		
		if($t === 'Submit Edits'){
			$ph = str_replace(array(' ', '(', ')', '-'), "", $this->contactPhone->getValue());
			
			
			$chk->setPayeeName($this->payeeName->getValue());
			$chk->setAccountNumber($this->payeeAccount->getValue());
			$chk->setContactFirstName($this->contactFirst->getValue());
			$chk->setContactLastName($this->contactLast->getValue());
			$chk->setPhone($ph);
			
			$chk->setCheckNumber($this->checkNum->getValue());
			$chk->setComment($this->commentText->getValue());
			
			
		}
		
		return $chk;
		
    }
	
	public function addAComment($t)
    {
		
		if($t === 'Add A Comment'){
			$this->commentText->setAttrib('readonly', null);
			
			
			$this->issueCheck->setAttrib('disabled', true);
			$this->denyCheck->setAttrib('disabled', true);
			$this->editCheck->setAttrib('disabled', true);
			
			$this->addComment->setLabel('Submit Comment');
		}
		
		if($t === 'Submit Comment'){
			$this->addComment->setLabel('Add A Comment');
			return $this->commentText->getValue();
		}
    }
	
	

}



