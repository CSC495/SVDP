<?php
class Application_Model_reportForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('login');
		$this->setAttrib('id', 'login');
		$this->setMethod('post');
		$this->setAction('/SVDP/public/report/process');  
		
               $report1 = $this->addElement('submit', 'report1', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Reimbursement Form',
               ));
               $report2 = $this->addElement('submit', 'report2', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'On Call Activities',
               ));
               $report3 = $this->addElement('submit', 'report3', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Client Information',
               )); 
	}
}
