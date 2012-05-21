<?php
class Application_Model_Report_reportForm extends Twitter_Bootstrap_Form_Vertical
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('report');
		$this->setAttrib('id', 'report');
		$this->setMethod('post');
		
		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/report/process/'));
		
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'report/indexFormViewScript.phtml'))
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
		
                $report1 = $this->addElement('submit', 'rReport', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
		   'class'    => 'btn btn-success',
		   'decorators' => array('ViewHelper'),
                ));
                $report2 = $this->addElement('submit', 'oReport', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
		   'class'    => 'btn btn-success',
		   'decorators' => array('ViewHelper'),
                ));
                $report3 = $this->addElement('submit', 'cReport', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Generate Report',
		   'class'    => 'btn btn-success',
		   'decorators' => array('ViewHelper'),
                )); 
	}
}
