<?php

class Application_Model_Treasurer_CheckForm extends Twitter_Bootstrap_Form_Horizontal 
{
    const INITIAL = 'INITIAL'; // Used to display all options on the check request form;
    const READONLY = 'READONLY'; // Used to display Comment State
    const EDIT = 'EDIT';     // Used to display edit functionality
    const COMMENT = 'COMMENT'; // Used to display Comment State
    const MEMBERVIEW = 'MEMBERVIEW'; // Used to display functions enabled for member
    
    private $_state;
    /**
     * The buttons that are visible to the user
     */
    private $_activeButtons = array();
    /**
     * The list of all the buttons associated with the form
     */
    private $_buttons = array();
    /**
     * Holds state of the check that the form is being constructed for
     */
    private $_checkStatus = 'P';
    
    public function __construct($check, $state = Application_Model_Treasurer_CheckForm::READONLY)
    {
	// Set the state of the form
	$this->_state = $state;
	$this->_checkStatus = $check->getStatus();
	
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

		$this->addElement('text', 'clientName', array(
		'readonly' => true,
		'required' => true,
		'label' => 'Client Name',
		));
		$this->clientName->setValue($case->getClient()->getFullName());
		
		$this->addElement('text', 'id',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum',),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check ID',
				'size'		 => 7,
		));
		$this->id->setValue($check->getID());
		
		
		$this->addElement('text', 'SVDPname',  array(
				'filters'    => array('StringTrim'),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Submitted By',
				'size'		 => 7,
		));
		$this->SVDPname->setValue($check->getUserFName() . ' ' . $check->getUserLName());
		
		$this->addElement('text', 'contactfname',  array(
				'filters'    => array('StringTrim'),
				'validators' => array(
				    array('StringLength', true, array(
					'max' => 30,
					'messages' => array(
					'stringLengthTooLong' => 'Last name must be shorter than 30 characters.',
				    ),)),
				),
				'readonly'   => true,
				'required'   => false,
				'label'      => 'Contact First Name',
		));
		$this->contactfname->setValue($check->getContactFirstName());
		
		$this->addElement('text', 'contactlname',  array(
				'filters'    => array('StringTrim'),
				'readonly'   => true,
				'validators' => array(
				    array('StringLength', true, array(
					'max' => 30,
					'messages' => array(
					'stringLengthTooLong' => 'Last name must be shorter than 30 characters.',
				    ),)),
				),
				'required'   => false,
				'label'      => 'Contact Last Name',
		));
		$this->contactlname->setValue($check->getContactLastName());
		
		
		$this->addElement('text', 'contactPhone',  array(
				'filters'   =>	array('StringTrim','Digits'),
				'validators' => array(
					array('StringLength', true, array(
						'min' => 10,
						'max' => 10,
						'messages' => array(
						'stringLengthTooShort' => 'Phone number must be 10 digits.',
						'stringLengthTooLong' => 'Phone number must be 10 digits.',
							)))),
				'readonly'  => 	true,
				'required'  => 	true,
				'label'     => 	'Contact Phone #',
				'size'		=> 	7,
				'class' 	=> 	'phone'
		));
		$this->contactPhone->setValue($check->getPhone());
		
		
		
		$this->addElement('text', 'amount',  array(
				'filters'    => array('StringTrim',new App_Filter_Money(),),
				'validators' => array('float'),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Amount',
		));
		$this->amount->setValue(App_Formatting::formatCurrency($check->getAmount()));
		
		
		$this->addElement('text', 'caseID',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum'),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Case ID',
		));
		$this->caseID->setValue($check->getCase());
		
		
		$this->addElement('text', 'requestDate',  array(
				'filters'    => array('StringTrim'),
				'validators' => array(
				    array('Date', true, array(
					'format' => 'MM/dd/yyyy',
					'messages' => array(
					'dateInvalidDate' => 'Birth date must be properly formatted.',
					'dateFalseFormat' => 'Birth date must be a valid date.',
					),
				    )),
				 ),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Requested',
				'size'		 => 10,
		));
		$this->requestDate->setValue(App_Formatting::formatDate($check->getRequestDate()));
		
		
		$this->addElement('text', 'checkNum',  array(
				'filters'    => array('StringTrim'),
				'validators' => array('Alnum'),
				'readonly'   => false,
				'required'   => false,
				'label'      => 'Real Check Number',
		));
		$this->checkNum->setValue($check->getCheckNumber());
		
		
		$this->addElement('text', 'issueDate',  array(
				'filters'    => array('StringTrim'),
				'validators' => array(
				    array('Date', true, array(
					'format' => 'MM/dd/yyyy',
					'messages' => array(
					'dateInvalidDate' => 'Birth date must be properly formatted.',
					'dateFalseFormat' => 'Birth date must be a valid date.',
					),
				    )),
				 ),
				'readonly'   => true,
				'required'   => false,
				'label'      => 'Check Issued Date',
				'class'       => 'date',
		));
		$this->issueDate->setValue(App_Formatting::formatDate($check->getIssueDate()));
		
		
		$this->addElement('text', 'payeeName',  array(
				'filters'    => array('StringTrim'),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Payee Name',
				'size'		 => 7,
		));
		$this->payeeName->setValue($check->getPayeeName());
		
		
		$addr = $check->getAddress();

		$this->addElement('text', 'address',  array(
				'filters'    => array('StringTrim'),
				'readonly'   => true,
				'required'   => false,
				'label'      => 'Payee Address',
				'size'		 => 7,
		));
		$this->address->setValue($addr);
		
		
		$this->addElement('text', 'payeeAccount',  array(
				'filters'    => array('StringTrim',),
				'validators' => array(array('StringLength', false, array(1, 30)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Payee Account #',
				'size'		 => 7,
		));
		$this->payeeAccount->setValue($check->getAccountNumber());
		
		
		$this->addElement('text', 'caseNeed',  array(
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Case Need',
		));
		$this->caseNeed->setValue($check->getCaseNeedName());
		
		
		
		$this->addElement('textarea', 'commentText', array(
                'label' => 'Comment',
                'required' => false,
                'filters' => array('StringTrim'),
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
		
	    $this->addElement('submit', 'issueCheck', array(
		    'label' => 'Issue Check Request',
		    'decorators' => array('ViewHelper'),
		    'class' => 'btn btn-success',
		    'id' => 'issue_check',
		    'required' => false,
	    ));
	    array_push($this->_buttons,$this->issueCheck);
	    
	    $this->addElement('submit', 'denyCheck', array(
		    'label' => 'Deny Check Request',
		    'decorators' => array('ViewHelper'),
		    'class' => 'btn btn-danger',
		    'id' => 'deny_check',
		    'required' => false,
	    ));
	    array_push($this->_buttons,$this->denyCheck);
	    
	    $this->addElement('submit', 'addComment', array(
		    'label' => 'Add A Comment',
		    'decorators' => array('ViewHelper'),
		    'class' => 'btn btn-info',
		    'id' => 'add_comment',
		    'required' => false,
	    ));
	    array_push($this->_buttons,$this->addComment);
	    
	    $this->addElement('submit', 'editCheck', array(
		    'label' => 'Edit Check Request',
		    'decorators' => array('ViewHelper'),
		    'class' => 'btn btn-info',
		    'id' => 'edit_check',
		    'required' => false,
	    ));
	    array_push($this->_buttons,$this->editCheck);
	    
	    $this->addElement('submit', 'submitEdits', array(
				'label' => 'Submit Edits',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-success',
				'id' => 'submit_edits',
				'required' => false,
	    ));
	    array_push($this->_buttons,$this->submitEdits);
	    
	    $this->addElement('submit', 'cancelEdits', array(
				'label' => 'Cancel Edits',
				'decorators' => array('ViewHelper'),
				'class' => 'btn btn-danger',
				'id' => 'cancel_edits',
				'required' => false,
	    ));
	    array_push($this->_buttons,$this->cancelEdits);
	    
	    $this->addElement('submit', 'submitComment', array(
		'label' => 'Submit Comment',
		'id' => 'submit_comment',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-success',
		'required' => false,
	    ));
	    array_push($this->_buttons,$this->submitComment);
	    
	    $this->addElement('submit', 'cancelComment', array(
		'label' => 'Cancel Comment',
		'id' => 'cancel_comment',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-danger',
		'required' => false,
	    ));
	    array_push($this->_buttons,$this->cancelComment);
	    
	    $this->addElement('submit', 'addComment', array(
		'label' => 'Add A Comment',
		'id' => 'add_comment',
		'decorators' => array('ViewHelper'),
		'class' => 'btn btn-info',
		'required' => false,
	    ));
	    array_push($this->_buttons,$this->addComment);
	    
	    // Set up address
	    $this->addSubForm(new Application_Model_Member_AddrSubForm(array(
            'title' => 'Payee address:',
            'zipRequired' => true,
	    'readOnly' => true,
	    )), 'addr' );
	    
	    $address = $check->getAddress();
	    $this->addr->setAddr($address);
	    
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
		if( $this->_state === Application_Model_Treasurer_CheckForm::MEMBERVIEW)
		{
		    $this->setMemberView();
		}
	    }
	    
    }
    /**
     * Returns a string representing which button was pressed
     */
    public function getAction()
    {
	foreach($this->_buttons as $btn)
	{
	    if($btn->isChecked()){
		return $btn->getId();
	    }
	}
	
    }
    /**
     * Gets current state of the form
     */
    public function getState()
    {
	return $this->_state;
    }
    public function getButtons()
    {
	return( $this->_activeButtons);
    }
    public function setMemberView()
    {
	array_push($this->_activeButtons,$this->addComment);
    }
    public function setCommentState()
    {
	array_push($this->_activeButtons,$this->submitComment);
	array_push($this->_activeButtons,$this->cancelComment);

	$this->commentText->setAttrib('readonly', null);
	
	$this->issueCheck->setAttrib('disabled', true);
	$this->denyCheck->setAttrib('disabled', true);
	$this->editCheck->setAttrib('disabled', true);
	
	$this->addComment->setLabel('Submit Comment');
    }
    public function setEditState()
    {
	array_push($this->_activeButtons,$this->submitEdits);
	array_push($this->_activeButtons,$this->cancelEdits);
	
	// Set editable field
	$this->amount->setAttrib('readonly', null);
	$this->payeeName->setAttrib('readonly', null);
	$this->payeeAccount->setAttrib('readonly', null);
	$this->contactfname->setAttrib('readonly', null);
	$this->contactlname->setAttrib('readonly', null);
	$this->contactPhone->setAttrib('readonly', null);
	$this->checkNum->setAttrib('readonly', null);
	$this->commentText->setAttrib('readonly', null);
	$this->addr->addrId->setAttrib('readonly', null);
	$this->addr->street->setAttrib('readonly', null);
	$this->addr->apt->setAttrib('readonly', null);
	$this->addr->city->setAttrib('readonly', null);
	$this->addr->state->setAttrib('readonly', null);
	$this->addr->zip->setAttrib('readonly', null);
		    
    }
    public function setInitialButtons()
    {
	// Only show issue, deny, edit if check if pending
	if($this->_checkStatus === 'P')
	{
	    array_push($this->_activeButtons,$this->issueCheck);
	    array_push($this->_activeButtons,$this->denyCheck);
	    array_push($this->_activeButtons,$this->editCheck);
	    
	    $this->checkNum->setAttrib('readonly', null);
	}
	array_push($this->_activeButtons,$this->addComment);
	
	
    }
    public function getComment()
    {
	return $this->commentText->getValue();
    }
    public function getCheckReq()
    {
	$service = new App_Service_TreasurerService();
	$check = $service->getCheckReqById($this->id->getValue());
	$check
	    ->setPayeeName($this->payeeName->getValue())
	    ->setContactFirstName($this->contactfname->getValue())
	    ->setContactLastName($this->contactlname->getValue())
	    ->setCheckNumber($this->checkNum->getValue())
	    ->setAmount($this->amount->getValue())
	    ->setAccountNumber($this->payeeAccount->getValue())
	    ->setPhone($this->contactPhone->getValue())
	    ->setAddress($this->addr->getAddr())
	    ->setComment($this->commentText->getValue());
	return $check;
    }

}



