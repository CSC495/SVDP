<?php
class Application_Model_Report_clientReport extends Twitter_Bootstrap_Form_Vertical
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('clientinfo');
		$this->setAttrib('id', 'clientinfo');
		$this->setMethod('post');
		
		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/report/clientresults/'));
		
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'report/clientinfoViewScript.phtml'))
		));	
		
		// Client Id(Integer)
		$clientID = $this->addElement('text', 'clientid', array(
			'filters'    => array('Digits'),
			'required'   => true,
			'class'	     => 'input-small',
			'validators' => array(
			array('Db_RecordExists', true, array(
			    'table' => 'client',
			    'field' => 'client_id',
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
