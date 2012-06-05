<?php
/**
 * Class provides the form which allows a user to log into the page
 */
class Application_Model_Login_LoginForm extends Twitter_Bootstrap_Form_Vertical
{
	/**
	 * Default constructor for form
	 *
	 * @param array $options Options to build super()
	 *
	 * @return null
	 */
	public function __construct($options = null){
		parent::__construct($options);
		$this->setName('login');
		$this->setAttrib('id', 'login');
		$this->setMethod('post');

		$baseUrl = new Zend_View_Helper_BaseUrl();
		$this->setAction($baseUrl->baseUrl('/login/login'));
		$this->setDecorators(array(
			array('ViewScript', array('viewScript' => 'login/loginViewScript.phtml'))
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
			'Wrapper',
		));
		
		// User Name
		$username = $this->addElement('text', 'username', array(
				   'required'   => true,
				   'label'      => 'Username:',
				   'class'      => 'input-large',
				 ));
		
	    // Password must consist of alphanumeric characters only
	    // must be be atleast 8 characters and have 1 digit
	    $password = $this->addElement('password', 'password', array(
                   'required'   => true,
                   'label'      => 'Password:',
				   'class'      => 'input-large',
               ));
        
		// Used to indicate errors
		$err = $this->addElement('hidden','err',array(
			'required' => false,
		));
		
		// Hidden element so next page knows referer. By the
		// forgot action in login controller. Forgot action will
		// only display if this prev=login is set
		$prev = $this->addElement('hidden','prev', array(
			'value' => 'login'
		));
		
	    $login = $this->addElement('submit', 'login', array(
                   'required' => false,
                   'ignore'   => true,
                   'label'    => '        Login        ',
				   'class'    => 'btn-success btn',
		           'decorators' => array('ViewHelper'),
                ));
               
		$forgot = $this->addElement('submit','forgot', array(
                    'required' => false,
                    'ignore' => true,
                    'label' => 'Forgot Password',
					'class' => 'btn-info btn',
					'decorators' => array('ViewHelper'),
                ));
	}
}
