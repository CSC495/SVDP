<?php

class Application_Model_SearchForm extends Zend_Form
{

    const TYPE_CLIENT_NAME = 'clientName';

    const TYPE_CLIENT_PHONE = 'clientPhone';

    const TYPE_CLIENT_ADDR = 'clientAddr';

    const TYPE_CLIENT_ID = 'clientId';

    const TYPE_CASE_ID = 'caseId';

    const TYPE_CHECK_REQ_ID = 'checkReqId';

    private $types = array(
        self::TYPE_CLIENT_NAME => array('label' => 'Client Phone Number'),
        self::TYPE_CLIENT_PHONE => array('label' => 'Client Phone Number'),
        self::TYPE_CLIENT_ADDR => array('label' => 'Client Address'),
    );

    public function __construct($action, $additionalTypes, $options = null)
    {
        parent::__construct($options);

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl("/search/$action"))
             ->setMethod('get')
             ->setDecorators(array('FormElements', 'Form'))
             ->setElementDecorators(array('ViewHelper', 'Label'));

        $this->types = array_merge($this->types, $additionalTypes);

        $this->addElement('select', 'type', array(
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'messages' => array('isEmpty' => 'You must enter a search criterion.'),
                )),
            ),
        ));

        $this->addElement('text', 'query', array(
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => array('string', 'space'),
                    'messages' => array('isEmpty' => 'You must enter a search query.'),
                )),
            ),
            'size' => 40,
        ));

        $this->addElement('submit', 'search', array(
            'label' => 'Search',
            'decorators' => array('ViewHelper'),
        ));

        foreach($this->types as $typeName => $typeInfo) {
            $this->type->addMultiOption($typeName, $typeInfo['label']);
        }
    }

    public function isValid($data)
    {
        // If this type of search requires additional validation, add the requisite validators.
        if (isset($data['type']) && isset($this->types[$data['type']])) {
            $typeInfo = $this->types[$data['type']];

            if (isset($typeInfo['validators'])) {
                $this->query->addValidators($typeInfo['validators']);
            }
        }

        // Defer to the usual Zend validation code.
        return parent::isValid($data);
    }
}
