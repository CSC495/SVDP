<?php

class Application_Model_Report_reimbursementReport extends Twitter_Bootstrap_Form_Vertical
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('reimburse');
		$this->setAttrib('id', 'reimburse');
		$this->setMethod('post');
		
		$baseUrl = new Zend_View_Helper_BaseUrl();
		//$this->setAction($baseUrl->baseUrl('/report/reimbursementresults/'));
		$this->setAction($baseUrl->baseUrl('/report/reimbursementresults/'));
		
		
		// Client Id(Integer)
		$caseID = $this->addElement('text', 'caseId', array(
			'filters'    => array('Digits'),
			'required'   => true,
			'class'	     => 'input-small',
			'validators' => array(
			array('Db_RecordExists', true, array(
			    'table' => 'checkRequest',
			    'field' => 'check_request',
			    'messages' => array(
				'noRecordFound' => 'No client was found for that ID.'
				)),                
			)),
		));
		
               $login = $this->addElement('submit', 'create', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Create Report',
		   'class'    => 'btn btn-success',
		   'decorators' => array('ViewHelper'),
               ));           
	}
}
