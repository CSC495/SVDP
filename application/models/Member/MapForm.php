<?php

/**
 * Address form allowing the user to look up potential clients on a Google map.
 */
class Application_Model_Member_MapForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_showNewClientButton = false;

    private $_similarClients = array();

    private $_directionsUrl = null;

    /**
     * Instantiates a new instance of the `Application_Model_Member_MapForm` class.
     */
    public function __construct()
    {
        $baseUrl = new Zend_View_Helper_BaseUrl();

        parent::__construct(array(
            'action' => $baseUrl->baseUrl(App_Resources::MEMBER),
            'method' => 'get',
            'decorators' => array(
                'PrepareElements',
                array('ViewScript', array(
                    'viewScript' => 'form/map-form.phtml',
                    'similarClients' => &$this->_similarClients,
                    'showNewClientButton' => &$this->_showNewClientButton,
                    'directionsUrl' => &$this->_directionsUrl,
                )),
                'Form',
            ),
            'class' => 'form-horizontal twocol',
        ));

        // Elements to collect general information about the potential client (optional):
        $this->addElement('text', 'firstName', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'First name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'First name',
            'maxlength' => 30,
            'dimension' => 3,
        ));

        $this->addElement('text', 'lastName', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong' => 'Last name must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'Last name',
            'maxlength' => 30,
            'maxlength' => 30,
            'dimension' => 3,
        ));

        // Elements to collect the potential client's address:
        $this->addSubForm(new Application_Model_Member_AddrSubForm('Client address:'), 'addr');

        // Elements that perform form actions:
        $this->addElement('submit', 'search', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label' => 'Submit',
            'decorators' => array('ViewHelper'),
        ));

        $this->addElement('submit', 'newClient', array(
            'label' => 'New Client',
            'decorators' => array('ViewHelper'),
        ));
    }

    /**
     * Renders the form, display the "New Client" button if and only if `showNewClientButton()` has
     * been called.
     *
     * @param Zend_View_Interface $view
     * @return string
     */
    public function render(Zend_View_Interface $view = null)
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
     * Returns the value of the form's first name field, or `null` if no first name was provided.
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return App_Formatting::emptyToNull($this->firstName->getValue());
    }

    /**
     * Returns the value of the form's last name field, or `null` if no last name was provided.
     *
     * @return string|null
     */
    public function getLastName()
    {
        return App_Formatting::emptyToNull($this->lastName->getValue());
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

        $this->_directionsUrl = 'http://maps.google.com/maps?daddr='
            . urlencode($addr->getFullAddr());

        return $this;
    }

    /**
     * Sets the form's list of similar clients.
     *
     * @param Application_Model_Impl_Client[] $similarClients
     * @return Application_Model_Member_MapForm
     */
    public function setSimilarClients($similarClients)
    {
        $this->_similarClients = $similarClients;
        return $this;
    }
}
