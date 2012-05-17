<?php
class Application_Model_Report_ocaReport extends Twitter_Bootstrap_Form_Vertical
{
	
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('clientinfo');
		$this->setAttrib('id', 'clientinfo');
		$this->setMethod('post');
		
		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/report/ocactivities/'));
		
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'report/ocaReportViewScript.phtml'))
		));
		
		// Start Date
		$startDate = $this->addElement('text', 'startDate', array(
                    'required' => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                        array('NotEmpty', true, array(
                            'type' => 'string',
                            'messages' => array('isEmpty' => 'Must choose a start date.'),
                        )),
                        array('Date', true, array(
                            'format' => 'MM/dd/yyyy',
                            'messages' => array(
                                'dateInvalidDate' => 'Must be properly formatted.',
                                'dateFalseFormat' => 'Must be a valid date.',
                            ),
                        )),
                    ),
                    'maxlength' => 10,
                    'class' => 'span2 date',
                ));
                // End Date
		$endDate = $this->addElement('text', 'endDate', array(
                    'required' => true,
                    'filters' => array('StringTrim'),
                    'validators' => array(
                        array('NotEmpty', true, array(
                            'type' => 'string',
                            'messages' => array('isEmpty' => 'Must choose a end date.'),
                        )),
                        array('Date', true, array(
                            'format' => 'MM/dd/yyyy',
                            'messages' => array(
                                'dateInvalidDate' => 'Must be properly formatted.',
                                'dateFalseFormat' => 'Must be a valid date.',
                            ),
                        )),
                    ),
                    'maxlength' => 10,
                    'class' => 'span2 date',
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