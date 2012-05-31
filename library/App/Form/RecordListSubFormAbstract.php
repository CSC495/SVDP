<?php

abstract class App_Form_RecordListSubFormAbstract extends Zend_Form_SubForm
{

    protected $_recordsSubForm;

    private $_safeSerializeService;

    private $_namespace;

    private $_labels;

    private $_readOnly;

    private $_narrow;

    private $_dirty;

    private $_legendMsg;

    private $_descriptionMsg;

    private $_addRecordMsg;

    private $_noRecordsMsg;

    private $_dirtyMsg;

    private $_dirtyField;

    private $_removedRecordsField;

    private $_removedRecordsHashField;

    private $_addRecordBtn;

    private $_submitBtn;

    public function __construct($options)
    {
        // Initialize necessary services.
        $this->_safeSerializeService = new App_Service_SafeSerialize();

        // Pass options on to the standard sub form class.
        if ($options instanceof Zend_Config) {
            $options = $options->toArray();
        }

        if (!is_array($options)) {
            throw new DomainException('Options must be an array or a config object.');
        }

        parent::__construct(array(
            'decorators' => array(
                array('ViewScript', array(
                    'viewScript' => 'form/record-list-sub-form-abstract.phtml',
                    'labels' => &$this->_labels,
                    'readOnly' => &$this->_readOnly,
                    'narrow' => &$this->_narrow,
                    'dirty' => &$this->_dirty,
                    'legendMsg' => &$this->_legendMsg,
                    'descriptionMsg' => &$this->_descriptionMsg,
                    'noRecordsMsg' => &$this->_noRecordsMsg,
                    'dirtyMsg' => &$this->_dirtyMsg,
                    'dirtyField' => &$this->_dirtyField,
                    'removedRecordsField' => &$this->_removedRecordsField,
                    'removedRecordsHashField' => &$this->_removedRecordsHashField,
                    'recordsSubForm' => &$this->_recordsSubForm,
                    'addRecordBtn' => &$this->_addRecordBtn,
                    'submitBtn' => &$this->_submitBtn,
                )),
            ),
            'elementDecorators' => array('ViewHelper'),
        ) + $options);

        // Save options for the record sub forms.
        if (!isset($options['namespace'])) {
            throw new InvalidArgumentException('Record list sub form namespace must be provided');
        }
        if (!isset($options['labels'])) {
            throw new InvalidArgumentException('Labels for record sub forms must be provided.');
        }

        $this->_namespace = $options['namespace'];
        $this->_labels    = $options['labels'];

        $this->_readOnly = (isset($options['readOnly'])) ? $options['readOnly'] : false;
        $this->_narrow   = (isset($options['narrow'])) ? $options['narrow'] : false;

        // Get custom message strings.
        $this->_legendMsg      = isset($options['legend']) ? $options['legend'] : 'Records:';
        $this->_descriptionMsg = isset($options['description']) ? $options['description'] : null;
        $this->_addRecordMsg   = isset($options['addRecordMsg'])
            ? $options['addRecordMsg']
            : 'Add Another Record';
        $this->_noRecordsMsg   = isset($options['noRecordsMsg'])
            ? $options['noRecordsMsg']
            : 'No records listed.';
        $this->_dirtyMsg   = isset($options['dirtyMsg'])
            ? $options['dirtyMsg']
            : 'Click "Submit" to save changes.';

        // Create hidden elements to hold removed records across POST requests.
        if (!$this->_readOnly) {
            $safeSerializedEmptyArray = $this->_safeSerializeService->serialize(array());

            $this->addElement('hidden', "{$this->_namespace}Dirty");

            $this->addElement('hidden', "{$this->_namespace}RecordsRemoved", array(
                'value' => $safeSerializedEmptyArray['serial'],
            ));

            $this->addElement('hidden', "{$this->_namespace}RecordsRemovedHash", array(
                'value' => $safeSerializedEmptyArray['hash'],
            ));

            $this->_dirtyField = $this->getElement("{$this->_namespace}Dirty");
            $this->_removedRecordsField = $this->getElement("{$this->_namespace}RecordsRemoved");
            $this->_removedRecordsHashField
                = $this->getElement("{$this->_namespace}RecordsRemovedHash");
        }

        // Create records sub form to hold the record sub forms. (It's sub forms all the way down!)
        $this->_recordsSubForm = new Zend_Form_SubForm(array(
            'decorators' => array('FormElements'),
            'elementDecorators' => array('ViewHelper'),
        ));

        $this->addSubForm($this->_recordsSubForm, "{$this->_namespace}Records");

        // Create add record button.
        if (!$this->_readOnly) {
            $this->addElement('submit', "{$this->_namespace}RecordAdd", array(
                'label' => $this->_addRecordMsg,
                'class' => 'btn btn-info',
            ));
        }

        $this->_addRecordBtn = $this->getElement("{$this->_namespace}RecordAdd");

        if (isset($options['submitMsg']) && !$this->_readOnly) {
            // If necessary, create submit changes button.
            $this->addElement('submit', "{$this->_namespace}Submit", array(
                'label' => $options['submitMsg'],
                'class' => 'btn btn-success',
            ));

            $this->_submitBtn = $this->getElement("{$this->_namespace}Submit");
        }
    }

    /**
     * Returns `true` if the submit button should will be rendered with a "dangerous" appearance,
     * and `false` otherwise.
     *
     * @return bool
     */
    public function isSubmitDanger()
    {
        return isset($this->_submitBtn)
            && $this->_submitBtn->getAttrib('class') === 'btn btn-success';
    }

    /**
     * Sets whether or not the submit button should be rendered with a "dangerous" appearance.
     *
     * @param bool $submitDanger
     * @return self
     */
    public function setSubmitDanger($submitDanger)
    {
        if (isset($this->_submitBtn)) {
            $this->_submitBtn->setAttrib('class',
                'btn btn-' . ($submitDanger ? 'danger' : 'success'));
        }
        return $this;
    }

    public function preValidate($data)
    {
        if (isset($data["{$this->_namespace}Dirty"]) && $data["{$this->_namespace}Dirty"]) {
            $this->_dirty = true;
        }

        if (!$this->_readOnly && isset($data["{$this->_namespace}Records"])) {
            foreach ($data["{$this->_namespace}Records"] as $recordName => $recordData) {
                $this->_recordsSubForm->addSubForm($this->createSubForm(), $recordName);
            }
        }
    }

    public function isValid($data)
    {
        $this->setDefaults($data);

        // Don't validate read only records.
        foreach ($this->_recordsSubForm->getSubForms() as $recordSubForm) {
            if ($this->isSubFormReadOnly($recordSubForm)) {
                foreach ($recordSubForm->getElements() as $element) {
                    $element
                        ->setRequired(false)
                        ->clearValidators();
                }
            }
        }

        return parent::isValid($data);
    }

    public function setDefaults(array $defaults)
    {
        parent::setDefaults($defaults);

        foreach ($this->_recordsSubForm->getSubForms() as $recordSubForm) {
            $this->updateSubFormElementReadOnlyFlags($recordSubForm);
        }

        return $this;
    }

    public function handleAddRemoveRecords($data)
    {
        if ($this->_readOnly) {
            return;
        }

        if (isset($data["{$this->_namespace}RecordAdd"])) {
            $this->addEmptyRecord();

            $this->_dirtyField->setValue('1');
            $this->_dirty = true;

            return true;
        } else if (isset($data["{$this->_namespace}Records"])) {
            foreach ($data["{$this->_namespace}Records"] as $recordName => $recordData) {
                if (isset($recordData['remove'])) {
                    $recordSubForm = $this->_recordsSubForm->getSubForm($recordName);

                    if (!$this->isSubFormReadOnly($recordSubForm)) {
                        $record = $this->getRecord($recordSubForm);

                        $this->_recordsSubForm->removeSubForm($recordName);

                        if ($record->getId() !== null) {
                            $removedRecords
                                = array_merge($this->getRemovedRecords(), array($record));
                            $safeSerializedRemovedRecords
                                = $this->_safeSerializeService->serialize($removedRecords);
                            $this->_removedRecordsField->setValue(
                                $safeSerializedRemovedRecords['serial']
                            );
                            $this->_removedRecordsHashField->setValue(
                                $safeSerializedRemovedRecords['hash']
                            );
                        }
                    }

                    $this->_dirtyField->setValue('1');
                    $this->_dirty = true;

                    return true;
                }
            }
        }

        return false;
    }

    public function getChangedRecords()
    {
        $changedRecords = array();

        if (!$this->_readOnly) {
            foreach ($this->_recordsSubForm->getSubForms() as $recordName => $recordSubForm) {
                if (!$this->isSubFormReadOnly($recordSubForm)) {
                    $changedRecords[$recordName] = $this->getRecord($recordSubForm);
                }
            }
        }

        return $changedRecords;
    }

    public function getRemovedRecords()
    {
        return !$this->_readOnly
            ? $this->_safeSerializeService->unserialize(
                $this->_removedRecordsField->getValue(),
                $this->_removedRecordsHashField->getValue()
            )
            : array();
    }

    public function setRecords($records)
    {
        $this->_recordsSubForm->clearSubForms();

        $i = 0;

        foreach ($records as $record)
        {
            $recordSubForm = $this->createSubForm();
            $this->setRecord($recordSubForm, $record);
            $this->_recordsSubForm->addSubForm($recordSubForm, $i++);
        }
    }

    public function addEmptyRecord()
    {
        $subFormKeys = array_keys($this->_recordsSubForm->getSubForms());

        $this->_recordsSubForm->addSubForm(
            $this->createSubForm(),
            $subFormKeys ? max($subFormKeys) + 1 : 0
        );
    }

    private function createSubForm()
    {
        $recordSubForm = new Zend_Form_SubForm();
        $recordSubForm
            ->addElementPrefixPath(
                'Twitter_Bootstrap_Form_Decorator',
                'Twitter/Bootstrap/Form/Decorator',
                'decorator'
            )
            ->setDecorators(array(
                'FormElements',
                array('HtmlTag', array('tag' => 'tr')),
            ))
            ->setElementDecorators(array(
                'FieldSize',
                'ViewHelper',
                'Addon',
                'ElementErrors',
                'Wrapper',
                array('HtmlTag', array('tag' => 'td')),
            ));

        $this->initSubForm($recordSubForm);

        if (!$this->_readOnly) {
            $safeSerializedFalse = $this->_safeSerializeService->serialize(false);

            $recordSubForm->addElement('hidden', 'readOnly', array(
                'value' => $safeSerializedFalse['serial'],
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'td', 'class' => 'remove', 'openOnly' => true)),
                ),
            ));

            $recordSubForm->addElement('button', 'remove', array(
                'decorators' => array('ViewHelper'),
                'class' => 'btn btn-danger',
                'escape'=> false,
                'type'=> 'submit',
            ));

            $recordSubForm->addElement('hidden', 'readOnlyHash', array(
                'value' => $safeSerializedFalse['hash'],
                'decorators' => array(
                    'ViewHelper',
                    array('HtmlTag', array('tag' => 'td', 'closeOnly' => true)),
                ),
            ));
        }

        $this->updateSubFormElementReadOnlyFlags($recordSubForm);

        return $recordSubForm;
    }

    private function updateSubFormElementReadOnlyFlags($recordSubForm)
    {
        $readOnly = $this->_readOnly || $this->isSubFormReadOnly($recordSubForm);

        foreach ($recordSubForm->getElements() as $element) {
            if ($element instanceof Zend_Form_Element_Select) {
                $element->setAttrib('disabled', $readOnly ? true : null);
            } else {
                $element->setAttrib('readonly', $readOnly ? true : null);
            }
        }

        if (!$this->_readOnly) {
            $removeElement = $recordSubForm->remove;

            if ($readOnly) {
                $removeElement->helper = 'formNote';
                $removeElement->setLabel('');
            } else {
                $removeElement->helper = 'formButton';
                $removeElement->setLabel('<i class="icon-remove icon-white"></i>');
            }
        }
    }

    protected function isSubFormReadOnly($recordSubForm)
    {
        if ($this->_readOnly) {
            return true;
        }

        return $this->_safeSerializeService->unserialize(
            $recordSubForm->readOnly->getValue(),
            $recordSubForm->readOnlyHash->getValue()
        );
    }

    protected function setSubFormReadOnly($recordSubForm, $readOnly)
    {
        if (!$this->_readOnly) {
            $safeSerializedReadOnly = $this->_safeSerializeService->serialize($readOnly);

            $recordSubForm->readOnly->setValue($safeSerializedReadOnly['serial']);
            $recordSubForm->readOnlyHash->setValue($safeSerializedReadOnly['hash']);

            $this->updateSubFormElementReadOnlyFlags($recordSubForm);
        }

        return $this;
    }

    protected abstract function initSubForm($recordSubForm);

    protected abstract function getRecord($recordSubForm);

    protected abstract function setRecord($recordSubForm, $record);
}
