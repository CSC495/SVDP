<?php
class Application_Model_Admin_AdminForm extends Zend_Form
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('admin');
		$this->setAttrib('id', 'admin');
		$this->setMethod('post');

        $baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/admin/process'));

                $user = $this->addElement('submit', 'user', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'View User Information',
                ));
                
                $adjust = $this->addElement('submit', 'adjust', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => 'Adjust Limits',
                ));
               
	}
}
