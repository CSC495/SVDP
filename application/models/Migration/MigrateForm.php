<?php

class Application_Model_Migration_MigrateForm extends Twitter_Bootstrap_Form_Horizontal
{

    private $_MARRIAGE_OPTIONS = array(
        '' => '',
        'Single' => 'Single',
        'Married' => 'Married',
        'Divorced' => 'Divorced',
        'Separated' => 'Separated',
        'Other' => 'Other',
    );

    private $_PARISH_OPTIONS = array(
        '' => '',
        'St. Raphael' => 'St. Raphael',
        'Holy Spirit' => 'Holy Spirit',
        'St. Elizabeth Seton' => 'St. Elizabeth Seton',
        'St. Thomas' => 'St. Thomas',
        'SS. Peter & Paul' => 'SS. Peter & Paul',
        'Other' => 'Other',
        'None' => 'None',
    );

    private $_CHANGE_TYPE_OPTIONS = array(
        '' => '',
        'move' => 'Moved',
        'edit' => 'Edited Address',
    );

    private $_id;

    private $_safeSerializeService;

    public function __construct($id = null, $readOnly = false)
    {        
        $baseUrl = new Zend_View_Helper_BaseUrl();
        
        parent::__construct(array(
            'action' => $baseUrl->baseUrl('/migration/index'),
            'method' => 'post',
            'class' => 'form-horizontal twocol',
        ));
        
        // Client data
        $this->addSubForm(new Application_Model_Migration_ClientForm(null, false), 'client');

        // Case data
        $this->addSubForm(new Application_Model_Migration_CaseNeedRecordListSubForm(false, false, null), 'caseneedrecordlist');
       

        // Primary form actions:

        $this->addElement('submit', 'submit', array(
            'label' => 'Create Client',
            'decorators' => array('ViewHelper'),
            'class' => 'btn btn-success',
        ));

        // If necessary, mark all the form elements read only.
        //foreach ($this->getElements() as $element) {
        //    if ($element instanceof Zend_Form_Element_Select) {
        //        $element->setAttrib('disabled', $readOnly ? true : null);
        //    } else {
        //        $element->setAttrib('readonly', $readOnly ? true : null);
        //    }
        //}
    }

    public function preValidate($data)
    {
        if (isset($data['maritalStatus']) && $data['maritalStatus'] === 'Married') {
            $this->spouseName->setRequired(true);
        }

        // XXX: This is kind of a hack, and it's slightly broken. We really should use a custom
        // validator class for our "at least one of these fields is required" logic.
        if (App_Formatting::isBlank($data['cellPhone'])
            && App_Formatting::isBlank($data['homePhone'])
            && App_Formatting::isBlank($data['workPhone'])) {
            $this->cellPhone->setRequired(true);
            $this->homePhone->setRequired(true);
            $this->workPhone->setRequired(true);
        }

        $this->householderRecordList->preValidate($data);
        $this->employerRecordList->preValidate($data);
    }

    
}
