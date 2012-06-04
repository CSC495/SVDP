<?php
/**
 * Class creates a form which allows a user to adjust various
 * Parish parameters such as Lifetime help limit, $ amount help limit per case
 * Total aid receievable in their lifetime, and number of yearly cases a client may have
 *
 */
class Application_Model_Admin_AdjustForm extends Twitter_Bootstrap_Form_Vertical
{
	/**
	 * Default constructor for the form which creates all the elements
	 *
	 * @return null
	 */
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
		));
		
		// Input of total aid a client can receive
		$aid = $this->addElement('text', 'aid', array(
			'filters'    => array( new App_Filter_Money(),
					      array('LocalizedToNormalized', false, array('precision', 2))),
			'validators' => array(
					array('Float',true,array(
						'messages' => array('notFloat' =>
						'Value is not valid dollar amount'))),
					array('GreaterThan',false,array("min" => -1, "messages" =>
									array("notGreaterThan" =>
									      "Value must be 0 or greater")))),
			'required'   => true,
			'label'      => 'Total Receivable Lifetime Aid:',
			'class'      => 'input-small',
			'prepend'    => '$',
			'name'       => 'aid',
			'id'         => 'aid',
		));


		// Input of funds for a particular case
		$casefund = $this->addElement('text', 'casefund', array(
			'filters'    => array( new App_Filter_Money(),
					      array('LocalizedToNormalized', false, array('precision', 2)),),
			'validators' => array(
					array('Float',true,array(
							'messages' => array('notFloat' =>
								'Value is not valid dollar amount'))),
					array('GreaterThan',false,array("min" => -1, "messages" =>
									array("notGreaterThan" =>
									      "Value must be 0 or greater")))),
			'required'   => true,
			'label'      => 'Total Receivable Aid Per Case:',
			'class'      => 'input-small',
			'prepend'    => '$',
			'name'       => 'casefund',
			'id'         => 'casefund',
		   ));
	       
		// Input of lifetime cases a client can have
		$lifetimecases = $this->addElement('text', 'lifetimecases', array(
		    'validators' => array('Int',
				   array('GreaterThan',false,
					 array("min" => -1, "messages" =>
						array("notGreaterThan" => "Value must be 0 or greater")))),
		    'required'   => true,
		    'label'      => 'Lifetime Case Limit:',
		    'class'      => 'input-small',
		    'name'       => 'lifetimecases',
		    'id'         => 'lifetimecases',
		   ));
        
		// Input of yearly cases a client can have
		$yearlycases = $this->addElement('text', 'yearlycases', array(
		   'validators' => array('Int',
				   array('GreaterThan',false,
					 array("min" => -1, "messages" =>
						array("notGreaterThan" => "Value must be 0 or greater")))),
		    'required'   => true,
		    'label'      => 'Yearly Cases Limit:',
		    'class'      => 'input-small',
		    'name'       => 'yearlycases',
		    'id'         => 'yearlycases',
		   ));
               
		$adjust = $this->addElement('submit', 'adjust', array(
		    'required' => false,
		    'ignore'   => true,
		    'label'    => 'Submit',
		    'class'    => 'btn btn-success',
		    'decorators' => array('ViewHelper'),
		));
               
	       //$jsparam = 'javascript:return adjust_validation(this)';
	       //$this->addAttribs(array('onSubmit'=>$jsparam));
	}
}
