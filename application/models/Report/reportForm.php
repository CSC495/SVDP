<?php
class Application_Model_Report_reportForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('login');
		$this->setAttrib('id', 'login');
		$this->setMethod('post');
		//$this->setAction('/SVDP/public/report/process');  
		
		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/report/process'));
		
		$report1Label = $this->addElement('text', 'report1Label', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Reimbursement Form',
                ));
                $report1 = $this->addElement('submit', 'report1', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
                ));
		$report2Label = $this->addElement('text', 'report2Label', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'On Call Activities',
                ));
                $report2 = $this->addElement('submit', 'report2', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
                ));
		$report3Label = $this->addElement('text', 'report3Label', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Client Information',
                ));
                $report3 = $this->addElement('submit', 'report3', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
                )); 
	}
}
