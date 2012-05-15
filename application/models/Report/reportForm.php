<?php
class Application_Model_Report_reportForm extends Twitter_Bootstrap_Form_Vertical
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('report');
		$this->setAttrib('id', 'report');
		$this->setMethod('post');
		
		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/report/process'));
		
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'report/indexFormViewScript.phtml'))
		));		
		
                $report1 = $this->addElement('submit', 'report1', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
		   'class'    => 'btn btn-success',
                ));
                $report2 = $this->addElement('submit', 'report2', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
		   'class'    => 'btn btn-success',
                ));
                $report3 = $this->addElement('submit', 'report3', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
		   'class'    => 'btn btn-success',
                )); 
	}
}
