<?php

/**
 * Abstract base class for both member and treasurer search forms.
 */
abstract class Application_Model_Search_FormAbstract extends Twitter_Bootstrap_Form_Inline
{

    /* Various types of searches that users can perform. */
    const TYPE_ALL          = 'all';
    const TYPE_CLIENT_NAME  = 'clientName';
    const TYPE_CLIENT_PHONE = 'clientPhone';
    const TYPE_CLIENT_ADDR  = 'clientAddr';
    const TYPE_CLIENT_ID    = 'clientId';
    const TYPE_CASE_ID      = 'caseId';
    const TYPE_CHECK_REQ_ID = 'checkReqId';

    /**
     * Search query types shared by all subclasses.
     *
     * @var string[][]
     */
    private $_types = array(
        self::TYPE_CLIENT_NAME => array('label' => 'Client Name'),
        self::TYPE_CLIENT_PHONE => array('label' => 'Client Phone Number'),
        self::TYPE_CLIENT_ADDR => array('label' => 'Client Address'),
    );

    /**
     * Initializes a new instance of the `Application_Model_SearchFormAbstract` class having the
     * specified form action and additional set of search types.
     *
     * @param string $action
     * @param string $listAllLabel
     * @param string[][] $additionalTypes
     */
    public function __construct($action, $listAllLabel, $additionalTypes)
    {
        // Initialize the form.
        parent::__construct();

        $baseUrl = new Zend_View_Helper_BaseUrl();

        $this->setAction($baseUrl->baseUrl("/search/$action"))
             ->setMethod('get');

        $this->_types = array_merge($this->_types, $additionalTypes);

        // Add form elements.
        $this->addElement('select', 'type', array(
            'required' => true,
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must select a search criterion.'),
                )),
            ),
        ));

        $this->addElement('text', 'query', array(
            'required' => true,
            'filters' => array('StringTrim'),
            'validators' => array(
                array('NotEmpty', true, array(
                    'type' => 'string',
                    'messages' => array('isEmpty' => 'You must enter a search query.'),
                )),
            ),
            'size' => 40,
            'class' => 'search-query',
        ));

        $this->addElement('submit', 'search', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_PRIMARY,
            'label' => 'Search',
        ));

        $this->addElement('submit', 'listAll', array(
            'buttonType' => Twitter_Bootstrap_Form_Element_Submit::BUTTON_INFO,
            'label' => "List All $listAllLabel",
        ));

        // Populate search type dropdown.
        foreach($this->_types as $typeName => $typeInfo) {
            $this->type->addMultiOption($typeName, $typeInfo['label']);
        }
    }

    /**
     * Checks if the specified GET data represents a valid form submission.
     *
     * @param array $data GET query data for the current request.
     * @return bool `true` if the GET data is valid, `false` otherwise.
     */
    public function isValid($data)
    {
        // If the request indicates that we should list all queries, don't perform any validation.
        if (isset($data['listAll'])) {
            $this->listAll->setValue(true);
            return true;
        }

        // If this type of search requires additional validation, add the requisite validators.
        if (isset($data['type']) && isset($this->_types[$data['type']])) {
            $typeInfo = $this->_types[$data['type']];

            if (isset($typeInfo['validators'])) {
                $this->query->addValidators($typeInfo['validators']);
            }
        }

        // Defer to the usual Zend validation code.
        return parent::isValid($data);
    }

    /**
     * Returns the type of search query requested, which shall be one of the `TYPE_` constants
     * defined above.
     *
     * @return string
     */
    public function getType()
    {
        return $this->listAll->isChecked() ? self::TYPE_ALL : $this->type->getValue();
    }

    /**
     * Returns the user's search query. Leading and trailing whitespace shall be trimmed from this
     * query; moreover, if the user searched for a phone number, all non-digit characters shall be
     * removed.
     *
     * @return string
     */
    public function getQuery()
    {
        $filters = new Zend_Filter();

        if ($this->getType() == self::TYPE_CLIENT_PHONE) {
            $filters->addFilter(new Zend_Filter_Digits());
        }

        return $filters->filter($this->query->getValue());
    }
}
