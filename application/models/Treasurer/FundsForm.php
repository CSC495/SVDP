<?php

class Application_Model_Treasurer_FundsForm extends Twitter_Bootstrap_Form_Horizontal
{

    public function __construct()
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();
		
        parent::__construct(array(
            'action' => $baseUrl->baseUrl(App_Resources::TREASURER) . '/updateFunds',
            'method' => 'post',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array('viewScript' => 'treasurer/fundsViewScript.phtml')),
                'Form',
            ),
        ));
		
		
		$funds = $this->addElement('text', 'funds', array(
				'filters'    => array('StringTrim',
				    array('LocalizedToNormalized', false, array('precision', 2))),
				'validators' => array(
				    array('Float',true,array(
						'messages' => array('notFloat' =>
						'Value is not valid dollar amount'))),
				    array('GreaterThan',false,array("min" => -1, "messages" =>
									array("notGreaterThan" =>
									      "Value must be 0 or greater"))),
				),
				'required'   => true,
				'label'      => 'Total Funds:',
				'size'		 => 7,
		));
		
		

        $this->addElement('submit', 'submit', array(
            'label' => 'Submit',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));
		
		//$service->updateParishFunds($funds);
    }

    /*public function preValidate($data)
    {
        //$this->  ->preValidate($data);
    }*/

    /*public function getFunds()
    {
        //$this->updateCurrentFunds->updateParishFunds($funds);
		//$service->updateParishFunds(300);
		
		//return $this->updateCurrentFunds->$funds;
		
		//return App_Formatting::emptyToNull($this->funds->getValue());
		//return $this->funds->getValue();
		//return 702220;
    }*/

}
