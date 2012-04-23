<?php
class Application_Model_MapForm extends Zend_Form
{

	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('member');
		$this->setAttrib('id', 'member');
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/member/map'));
		
	}
}