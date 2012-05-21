<?php

class Application_Model_Treasurer_CheckForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function __construct($check)
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

		
		
        parent::__construct(array(
            'action' => $baseUrl->baseUrl(App_Resources::TREASURER) . '/updateFunds',
            'method' => 'post',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array('viewScript' => 'treasurer/checkViewScript.phtml')),
                'Form',
            ),
        ));
		
		
		$this->addElement('text', 'checkID',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check ID',
				'size'		 => 7,
		));
		$this->checkID->setValue($check->getID());
		
		
		$this->addElement('text', 'SVDPname',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'SVDP Member Name',
				'size'		 => 7,
		));
		$this->SVDPname->setValue($check->getUser());
		
		
		$this->addElement('text', 'contact',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Contact Name',
				'size'		 => 7,
		));
		$this->contact->setValue($check->getContactFirstName() . ' ' . $check->getContactLastName());
		
		
		$this->addElement('text', 'contactPhone',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Contact Phone #',
				'size'		 => 7,
		));
		$this->contactPhone->setValue($check->getPhone());
		
		
		
		$this->addElement('text', 'amount',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Amount',
				'size'		 => 7,
		));
		$this->amount->setValue($check->getAmount());
		
		
		$this->addElement('text', 'caseID',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Case ID',
				'size'		 => 7,
		));
		$this->caseID->setValue($check->getCase());
		
		
		$this->addElement('text', 'requestDate',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Requested',
				'size'		 => 7,
		));
		$this->requestDate->setValue($check->getRequestDate());
		
		
		$this->addElement('text', 'checkNum',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Real Check Number',
				'size'		 => 7,
		));
		$this->checkNum->setValue($check->getCheckNumber());
		
		
		$this->addElement('text', 'issueDate',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Check Issued Date',
				'size'		 => 7,
		));
		$this->issueDate->setValue($check->getIssueDate());
		
		
		$this->addElement('text', 'payeeName',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Payee Name',
				'size'		 => 7,
		));
		$this->payeeName->setValue($check->getPayeeName());
		
		$addr = $check->getAddress();
		$this->addElement('text', 'address',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
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
		$this->caseNeed->setValue($check->getCaseNeedID());
		//$this->caseNeed->setValue($this->escape($check->getCase()->getNeedList()));
		
		
		$this->addElement('text', 'comment',  array(
				'filters'    => array('StringTrim',	array('LocalizedToNormalized', 
										false, array('precision', 2))),
				'validators' => array('Alnum', array('StringLength', false, array(1, 7)),),
				'readonly'   => true,
				'required'   => true,
				'label'      => 'Comment',
				'size'		 => 15,
		));
		$this->comment->setValue($check->getComment());
		
		
		
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
		
		

        $this->addElement('submit', 'submit', array(
            'label' => 'Submit Changes',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));
		
    }

    public function preValidate($data)
    {
        //$this->  ->preValidate($data);
    }

    public function getFunds()
    {
        //$this->updateCurrentFunds->updateParishFunds($funds);
		//$service->updateParishFunds(300);
		
		//return $this->updateCurrentFunds->$funds;
		
		//return App_Formatting::emptyToNull($this->funds->getValue());
		//return $this->funds->getValue();
		return 702220;
    }

}








/*
class Application_Model_Treasurer_CheckForm extends Twitter_Bootstrap_Form_Horizontal
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('new');
		$this->setAttrib('id', 'new');
		$this->setMethod('post');
		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/treasurer/checkReq'));
		$this->setDecorators(array(
			array('ViewScript',array('viewScript' => 'treasurer/ViewScript.phtml'))
		));
		
		$this->addElementPrefixPath(
			'Twitter_Bootstrap_Form_Decorator',
			'Twitter/Bootstrap/Form/Decorator',
			'decorator'
		);
		
		$this->setElementDecorators(array(
			'FieldSize',
			'ViewHelper',
			'Addon',
			'ElementErrors',
			array('Description', array('class' => 'help-block')),
			array('HtmlTag', array('tag' => 'div', 'class' => 'controls')),
			array('Label', array('class' => 'control-label')),
			'Wrapper',
		));
		/*
		// The memebers name
		$firstname = $this->addElement('text', 'firstname', array(
                                   'filters'    => array('StringTrim'),
				   'required'   => true,
				   'label'      => 'First Name:',
				 ));
		
		// The memebrs name
		$lastname = $this->addElement('text', 'lastname', array(
                                   'filters'    => array('StringTrim'),
				   'required'   => true,
				   'label'      => 'Last Name:',
				 ));
		
                // Members phone number
                $home = $this->addElement('text', 'home', array(
                   'filters'    => array('StringTrim','Digits'),
                   'required'   => false,
                   'label'      => 'Home Phone:',
		   'validators' => array(
			array('StringLength', true, array(
				'min' => 10,
				'max' => 10,
				'messages' => array(
				'stringLengthTooShort' => 'Phone number must be 10 digits.',
				'stringLengthTooLong' => 'Phone number must be 10 digits.',
                    )))),
                ));
        
		// Members other phone
		$cell = $this->addElement('text', 'cell', array(
                   'filters'    => array('StringTrim','Digits'),
                   'required'   => false,
                   'label'      => 'Cell Phone:',
		   'validators' => array(
			array('StringLength', true, array(
				'min' => 10,
				'max' => 10,
				'messages' => array(
				'stringLengthTooShort' => 'Phone number must be 10 digits.',
				'stringLengthTooLong' => 'Phone number must be 10 digits.',
                    )))),
                ));
		
		// IMemebers e-mail
                $email = $this->addElement('text', 'email', array(
			'filters'    => array('StringTrim'),
			'validators' => array('EmailAddress'),
			'required'   => true,
			'label'      => 'Email:',
                ));
               
	        // Type of memebr
                $role = $this->addElement('select','role',array(
			'label' => 'Member Type:',
			'value' => App_Roles::MEMBER,
			'multiOptions' => array ( 'M'   => 'Member',
						  App_Roles::ADMIN     => 'Admin',
						  App_Roles::TREASURER => 'Treasurer',)
			,));
               
                $adjust = $this->addElement('submit', 'submit', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Add New Contact',
                   'class'    => 'btn btn-success',
		   'decorators' => array('ViewHelper'),
                ));
               
	}
}
*/