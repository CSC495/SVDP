<?php
class Application_Model_Admin_AdjustForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('adjust');
		$this->setAttrib('id', 'adjust');
		$this->setMethod('post');

        $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/admin/adjust'));
		
		// Input of total aid a client can recieve
		$aid = $this->addElement('text', 'aid', array(
                                   'filters'    => array('StringTrim', 'StringToLower'),
                                  'validators' => array(
				          'Alnum',
		                        ),
				   'required'   => true,
				   'label'      => 'Total Yearly Limit:',
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
               
	       $jsparam = 'javascript:return adjust_validation(this)';
	       $this->addAttribs(array('onSubmit'=>$jsparam));
	}
}
