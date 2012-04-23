<?php
class Application_Model_AdjustForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('login');
		$this->setAttrib('id', 'login');
		$this->setMethod('post');
		$this->setAction('/SVDP/public/admin/adjustprocess');
		
		// Input of total aid a client can recieve
		$aid = $this->addElement('text', 'aid', array(
                                   'filters'    => array('StringTrim', 'StringToLower'),
                                  'validators' => array(
				          'Alnum',
		                        ),
				   'required'   => true,
				   'label'      => 'Total Recievable Aid:',
				 ));
		
               // Input of lifetime cases a client can have
               $lifetimecases = $this->addElement('text', 'lifetimecases', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Lifetime Cases:',
               ));
        
		// Input of lifetime cases a client can have
               $yearlycases = $this->addElement('text', 'yearlycases', array(
                   'filters'    => array('StringTrim'),
                   'required'   => true,
                   'label'      => 'Yearly Cases:',
               ));
               
               $adjust = $this->addElement('submit', 'adjust', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Adjust Client Limits',
                ));
               
	}
}
