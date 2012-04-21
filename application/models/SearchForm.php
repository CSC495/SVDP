<?php

class Application_Model_SearchForm extends Zend_Form
{

    private $types = array(
        'clientPhone' => 'Client Phone Number',
        'clientAddr' => 'Client Address',
        'clientId' => 'Client #',
        'caseId' => 'Case #',
    );

    public function __construct($action, $additionalTypes, $options = null)
    {
        parent::__construct($options);

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl("/search/$action"))
             ->setMethod('get')
             ->setDecorators(array('FormElements', 'FormErrors', 'Form'))
             ->setElementDecorators(array(array('ViewHelper'), array('Errors'), array('Label')));

        $this->addElement('select', 'type', array(
            'required' => true,
            'multiOptions' => array_merge($this->types, $additionalTypes),
        ));

        $this->addElement('text', 'query', array(
            'required' => true,
            'size' => 40,
        ));

        $this->addElement('submit', 'search', array(
            'label' => 'Search',
            'decorators' => array('ViewHelper'),
        ));
    }
}
