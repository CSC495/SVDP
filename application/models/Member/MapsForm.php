<?php

/**
 * Address form allowing the user to look up potential clients on a Google map.
 */
class Application_Model_Member_MapsForm extends Twitter_Bootstrap_Form_Horizontal {

    /**
     * Instantiates a new instance of the `Application_Model_Member_MapsForm` class.
     */
    public function __construct()
    {
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl(App_Resources::MEMBER) . '/map')
             ->setMethod('get');

        $this->addElement('text', 'street', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a street address.'),
                )),
                array('StringLength', true, array(
                    'max' => 100,
                    'messages' => array(
                        'stringLengthTooLong'
                            => 'Street address must be shorter than 100 characters.',
                    ),
                )),
            ),
            'label' => 'Street address',
            'maxLength' => 100,
            'dimension' => 3,
        ));

        $this->addElement('text', 'apt', array(
            'filters' => array('StringTrim'),
            'validators' => array(
                array('StringLength', true, array(
                    'max' => 30,
                    'messages' => array(
                        'stringLengthTooLong'
                            => 'Apartment number must be shorter than 30 characters.',
                    ),
                )),
            ),
            'label' => 'Apartment #',
            'description' => '(optional)',
            'maxLength' => 30,
            'dimension' => 1,
        ));

        $this->addElement('text', 'city', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a city.'),
                )),
                array('StringLength', true, array(
                    'max' => 50,
                    'messages' => array(
                        'stringLengthTooLong'
                            => 'City must be shorter than 50 characters.',
                    ),
                )),
            ),
            'label' => 'City',
            'value' => 'Naperville',
            'maxLength' => 50,
            'dimension' => 3,
        ));

        $this->addElement('text', 'state', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a state.'),
                )),
                array('StringLength', true, array(
                    'min' => 2,
                    'max' => 2,
                    'messages' => array(
                        'stringLengthTooShort' => 'State must be a two character abbreviation.',
                        'stringLengthTooLong' => 'State must be a two character abbreviation.',
                    ),
                )),
            ),
            'label' => 'State',
            'value' => 'IL',
            'maxLength' => 2,
            'dimension' => 1,
        ));

        $this->addElement('text', 'zip', array(
            'validators' => array(
                array('Digits', true, array(
                    'messages' => array('notDigits' => 'ZIP code must be numeric.'),
                )),
                array('StringLength', true, array(
                    'min' => 5,
                    'max' => 5,
                    'messages' => array(
                        'stringLengthTooShort' => 'ZIP code must be five digits long.',
                        'stringLengthTooLong' => 'ZIP code must be five digits long.',
                    ),
                )),
            ),
            'label' => 'ZIP code',
            'description' => '(optional)',
            'maxLength' => 5,
            'dimension' => 1,
        ));

        $this->addElement('submit', 'search', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label' => 'Search',
        ));

        $this->addDisplayGroup(
            array('street', 'apt', 'city', 'state', 'zip'),
            'fields',
            array('legend' => "Enter a client's address.")
        );

        $this->addDisplayGroup(
            array('search'),
            'actions',
            array('disableLoadDefaultDecorators' => true, 'decorators' => array('Actions'))
        );
    }

    /**
     * Returns an address model object based on the form's current contents.
     *
     * @return Application_Model_Impl_Addr
     */
    public function getAddr()
    {
        $addr = new Application_Model_Impl_Addr();
        $addr->setStreet($this->street->getValue() !== '' ? $this->street->getValue() : null)
             ->setApt($this->apt->getValue() !== '' ? $this->apt->getValue() : null)
             ->setCity($this->city->getValue() !== '' ? $this->city->getValue() : null)
             ->setState($this->state->getValue() !== '' ? $this->state->getValue() : null)
             ->setZip($this->zip->getValue() !== '' ? $this->zip->getValue() : null);
        return $addr;
    }

    /**
     * Sets the form's current contents based on the specified address model object.
     *
     * @param Application_Model_Impl_Addr $addr
     * @return Application_Model_Member_MapsForm
     */
    public function setAddr($addr)
    {
        $this->street->setValue($addr->getStreet());
        $this->apt->setValue($addr->getApt());
        $this->city->setValue($addr->getCity());
        $this->state->setValue($addr->getState());
        $this->zip->setValue($addr->getZip());
    }
}
