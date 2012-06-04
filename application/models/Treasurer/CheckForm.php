<?php

class Application_Model_Treasurer_CheckForm extends Twitter_Bootstrap_Form_Horizontal 
{
    const INITIAL = 'INITIAL'; // Used to display all options on the check request form;
    const READONLY = 'READONLY'; // Used to display Comment State
    const EDIT = 'EDIT';     // Used to display edit functionality
    const COMMENT = 'COMMENT'; // Used to display Comment State
    
    private $_state;
    
    public function __construct($check, $state = Application_Model_Treasurer_CheckForm::READONLY)
    {
	// Set the state of the form
	$this->_state = $state;
	
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
		
		
		$this->addElement('text', 'contact',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Contact Name',
				'size'		 => 7,
		));
		$this->contact->setValue($check->getContactFirstName() . ' ' . $check->getContactLastName());
		
		
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
		));
		$this->amount->setValue($check->getAmount());
		
		
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
		//$this->requestDate->setValue($check->getRequestDate());
		
		
		$this->addElement('text', 'checkNum',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Real Check Number',
				'size'		 => 7,
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
		$this->addElement('text', 'address',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Payee Address',
				'size'		 => 7,
		));
		$this->address->setValue($addr);
		
		
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
		//$this->caseNeed->setValue($this->escape($check->getCase()->getNeedList()));
		
		
		
		$this->addElement('textarea', 'commentText', array(
                'label' => 'Comment',
                'required' => true,
                'filters' => array('StringTrim'),
                'validators' => array(
                    array('NotEmpty', true, array(
                        'type' => 'string',
                        'messages' => array('isEmpty' => 'You must enter a comment.'),
                    )),
                ),
                'dimension' => 7,
                'rows' => 4,
				'readonly' => true,
            ));
		$this->commentText->setValue($check->getComment());
		
		
		
		/*
		$this->addElement('text', '',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => '',
				'size'		 => 7,
		));
		$this->->setValue($check->get());
		*/
		
		$this->addElement('text', 'funds', array(
				'filters'    => array('StringTrim',
				array('LocalizedToNormalized', false, array('precision', 2))),
				'validators' => array(
						'Alnum',
						array('StringLength', false, array(1, 7)),
				),
				'required'   => true,
				'label'      => 'Current Funds:',
				'size'		 => 7,
		));
		
		
		if($check->getStatus() === 'P'){
			
		    // Set which buttons are shown
		    if( $this->_state === Application_Model_Treasurer_CheckForm::INITIAL)
		    {
			$this->setInitialButtons();
		    }
		    if( $this->_state === Application_Model_Treasurer_CheckForm::EDIT )
		    {
			$this->setEditable();
		    }
		    if( $this->_state === Application_Model_Treasurer_CheckForm::COMMENT )
		    {
			$this->setCommentState();
		    }
		}
		
    }
    public function getState()
    {
	return $this->_state;
    }
    public function getButtons()
    {
	switch( $this->_state )
	{
	    case Application_Model_Treasurer_CheckForm::COMMENT:
		return array($this->submitComment, $this->cancelComment);
		break;
	    case Application_Model_Treasurer_CheckForm::EDIT:
		return array($this->editCheck, $this->cancelEdits);
		break;
	    case Application_Model_Treasurer_CheckForm::INITIAL:
		return array($this->issueCheck, $this->denyCheck, $this->addComment, $this->editCheck);
		break;
	    default:
		return array();
		return;
	}
	
    }
    public function setCommentState()
    {
	$this->addElement('submit', 'submitComment', array(
		'label' => 'Submit Comment',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-success',
	));
	
	$this->addElement('submit', 'cancelComment', array(
		'label' => 'Cancel Comment',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-success',
	));

	$this->commentText->setAttrib('readonly', null);
	
	$this->issueCheck->setAttrib('disabled', true);
	$this->denyCheck->setAttrib('disabled', true);
	$this->editCheck->setAttrib('disabled', true);
	
	$this->addComment->setLabel('Submit Comment');
    }
    public function setEditable()
    {
	$this->addElement('submit', 'editCheck', array(
				'label' => 'Submit Edits',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-success',
				'value' => 'Submit Edits'
			));
	$this->addElement('submit', 'cancelEdits', array(
				'label' => 'Submit Edits',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-success',
				'value' => 'Cancel Edits',
			));
	
	// Set editable field
	$this->amount->setAttrib('readonly', null);
	$this->payeeName->setAttrib('readonly', null);
	$this->payeeAccount->setAttrib('readonly', null);
	$this->contact->setAttrib('readonly', null);
	$this->contactPhone->setAttrib('readonly', null);
	$this->checkNum->setAttrib('readonly', null);
	$this->issueDate->setAttrib('readonly', null);
	$this->caseNeed->setAttrib('readonly', null);
	$this->commentText->setAttrib('readonly', null);
			
    }
    public function setInitialButtons()
    {
	
	$this->addElement('submit', 'issueCheck', array(
		'label' => 'Issue Check Request',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-success',
	));
	
	$this->addElement('submit', 'denyCheck', array(
		'label' => 'Deny Check Request',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-success',
	));
	
	$this->addElement('submit', 'addComment', array(
		'label' => 'Add A Comment',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-success',
	));
	
	$this->addElement('submit', 'editCheck', array(
		'label' => 'Edit Check Request',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-success',
	));
    }
    public function preValidate($data)
    {
        //$this->  ->preValidate($data);
    }
}



