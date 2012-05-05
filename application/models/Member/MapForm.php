<?php

/**
 * Address form allowing the user to look up potential clients on a Google map.
 */
class Application_Model_Member_MapForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_showNewClientButton = false;

    /**
     * Instantiates a new instance of the `Application_Model_Member_MapForm` class.
     */
    public function __construct()
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl(App_Resources::MEMBER) . '/map')
             ->setMethod('get');

        $this->addSubForm(
            new Application_Model_Member_AddrSubForm("Enter a client's address."),
            'addr'
        );

        $this->addElement('submit', 'search', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label' => 'Search',
        ));

        $this->addElement('submit', 'newClient', array('label' => 'New Client'));

        $this->addDisplayGroup(
            array('search', 'newClient'),
            'actions',
            array('disableLoadDefaultDecorators' => true, 'decorators' => array('Actions'))
        );
    }

    /**
     * Renders the form, display the "New Client" button if and only if `showNewClientButton()` has
     * been called.
     *
     * @param Zend_View_Interface $view
     * @return string
     */
    public function render($view = null)
    {
        if (!$this->_showNewClientButton) {
            $this->removeElement('newClient');
        }

        return parent::render($view);
    }

    /**
     * Enables displaying the "New Client" button.
     */
    public function showNewClientButton()
    {
        $this->_showNewClientButton = true;
    }

    /**
     * Returns true if the request requires creation of a new client and false otherwise.
     *
     * @return bool
     */
    public function isNewClientRequest()
    {
        return $this->newClient->isChecked();
    }

    /**
     * Returns an address model object based on the form's current contents.
     *
     * @return Application_Model_Impl_Addr
     */
    public function getAddr()
    {
        return $this->addr->getAddr();
    }

    /**
     * Sets the form's current contents based on the specified address model object.
     *
     * @param Application_Model_Impl_Addr $addr
     * @return Application_Model_Member_MapForm
     */
    public function setAddr($addr)
    {
        $this->addr->setAddr($addr);
        return $this;
    }
}
