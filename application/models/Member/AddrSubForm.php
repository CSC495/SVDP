<?php

/**
 * Sub form containing common address widgets.
 */
class Application_Model_Member_AddrSubForm extends Twitter_Bootstrap_Form_Horizontal
{

    /**
     * Choices for the parish dropdown list.
     *
     * @var string[]
     */
    private $_PARISH_OPTIONS = array(
        '' => '',
        'St. Raphael' => 'St. Raphael',
        'Holy Spirit' => 'Holy Spirit',
        'St. Elizabeth Seton' => 'St. Elizabeth Seton',
        'St. Thomas' => 'St. Thomas',
        'SS. Peter & Paul' => 'SS. Peter & Paul',
        'Other' => 'Other',
    );

    /**
     * Instantiates a new instance of the `Application_Model_Member_AddrSubForm` class. The
     * following form options may be provided:
     *
     * * `hasParishField` (default `false`): If `true`, displays a "parish of residence" text field
     * * `readOnly` (default `false`): If `true`, all form elements will be marked read only
     * * `title` (default `null`): Sets the legend of a `fieldset` element rendered around the form
     * * `zipRequired` (default `false`): If `true`, the ZIP code text field will be required
     *
     * @param array $options
     */
    public function __construct(array $options)
    {
        parent::__construct(array(
            'isArray' => true,
            'decorators' => array('FormElements'),
        ));

        if (!empty($options['readOnly'])) {
            $readonlyAttr = 'readonly';
            $disabledAttr = 'disabled';
        } else {
            $readonlyAttr = null;
            $disabledAttr = null;
        }

        $this->addElement('hidden', 'addrId', array('decorators' => array('ViewHelper')));

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
            'readonly' => $readonlyAttr,
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
            'description' => '(Optional)',
            'maxLength' => 30,
            'dimension' => 1,
            'readonly' => $readonlyAttr,
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
            'readonly' => $readonlyAttr,
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
            'readonly' => $readonlyAttr,
        ));

        $this->addElement('text', 'zip', array(
            'required' => !empty($options['zipRequired']),
            'validators' => array(
                array('NotEmpty', true, array(
                    'messages' => array('isEmpty' => 'ZIP code must be present.'),
                )),
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
            'description' => !empty($options['zipRequired']) ? null : '(Optional)',
            'maxLength' => 5,
            'dimension' => 1,
            'readonly' => $readonlyAttr,
        ));

        $elements = array('street', 'apt', 'city', 'state', 'zip');

        if (!empty($options['hasParishField'])) {
            $this->addElement('select', 'resideParish', array(
                'multiOptions' => $this->_PARISH_OPTIONS,
                'required' => true,
                'validators' => array(
                    array('NotEmpty', true, array(
                        'type' => 'string',
                        'messages' => array('isEmpty' => 'You must choose a parish name.'),
                    )),
                    array('InArray', true, array(
                        'haystack' => array_keys($this->_PARISH_OPTIONS),
                        'strict' => true,
                        'messages' => array('notInArray' => 'You must choose a parish name.'),
                    )),
                ),
                'label' => 'Parish of residence',
                'dimension' => 3,
                'disabled' => $disabledAttr,
            ));

            $elements[] = 'resideParish';
        } else {
            $this->addElement('hidden', 'resideParish', array(
                'decorators' => array('ViewHelper'),
            ));
        }

        $this->addDisplayGroup($elements, 'addr', array(
            'legend' => isset($options['title']) ? $options['title'] : null,
        ));
    }

    /**
     * Returns an address model object based on the form's current contents.
     *
     * @return Application_Model_Impl_Addr
     */
    public function getAddr()
    {
        $addr = new Application_Model_Impl_Addr();
        $addr
            ->setId(App_Formatting::emptyToNull($this->addrId->getValue()))
            ->setStreet(App_Formatting::emptyToNull($this->street->getValue()))
            ->setApt(App_Formatting::emptyToNull($this->apt->getValue()))
            ->setCity(App_Formatting::emptyToNull($this->city->getValue()))
            ->setState(App_Formatting::emptyToNull($this->state->getValue()))
            ->setZip(App_Formatting::emptyToNull($this->zip->getValue()))
            ->setParish(App_Formatting::emptyToNull($this->resideParish->getValue()));

        return $addr;
    }

    /**
     * Sets the form's current contents based on the specified address model object.
     *
     * @param Application_Model_Impl_Addr $addr
     * @return self
     */
    public function setAddr(Application_Model_Impl_Addr $addr)
    {
        $this->addrId->setValue($addr->getId());
        $this->street->setValue($addr->getStreet());
        $this->apt->setValue($addr->getApt());
        $this->city->setValue($addr->getCity());
        $this->state->setValue($addr->getState());
        $this->zip->setValue($addr->getZip());
        $this->resideParish->setValue($addr->getParish());

        return $this;
    }
}
