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
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'admin/limitsViewScript.phtml'))
		));	
		// Input of total aid a client can recieve
		$aid = $this->addElement('text', 'aid', array(
			'filters'    => array( new App_Filter_Money() ),
			'validators' => array('Float','Int'),
			'required'   => false,
			'label'      => 'Total Recievable Lifetime Aid:',
			'class'      => 'input-small',
		));
		
		// Input of funds for a particular case
               $casefund = $this->addElement('text', 'casefund', array(
			'filters'    => array( new App_Filter_Money() ),
			'validators' => array('Float','Int'),
			'required'   => false,
			'label'      => 'Total Recievable Aid PER Case:',
			'class'      => 'input-small',
               ));
	       
               // Input of lifetime cases a client can have
               $lifetimecases = $this->addElement('text', 'lifetimecases', array(
		   'validators' => array('Digits'),
                   'required'   => false,
                   'label'      => 'Lifetime Case Limit:',
		   'class'      => 'input-small',
               ));
        
		// Input of yearly cases a client can have
               $yearlycases = $this->addElement('text', 'yearlycases', array(
		   'validators' => array('Digits'),
                   'required'   => false,
                   'label'      => 'Yearly Cases Limit:',
		   'class'      => 'input-small',
               ));
               
               $adjust = $this->addElement('submit', 'adjust', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Submit Changes',
		   'class'    => 'btn btn-success',
                ));
               
	       $jsparam = 'javascript:return adjust_validation(this)';
	       $this->addAttribs(array('onSubmit'=>$jsparam));
	}
}
