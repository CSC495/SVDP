<?php
class Application_Model_AdminForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('admin');
		$this->setAttrib('id', 'admin');
		$this->setMethod('post');
		$this->setAction('/SVDP/public/admin/process');

                $update = $this->addElement('submit', 'user', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'View User Information',
                ));
                
                $update = $this->addElement('submit', 'adjust', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Adjust Limits',
                ));
               
	}
}
