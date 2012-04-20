<?php
class Application_Model_UpdateFundForm extends Zend_Form
{
    public function __construct($options = null){
            parent::__construct($options);
            $this->setName('fund');
            $this->setAttrib('id', 'fund');
            $this->setMethod('post');
            $this->setAction('/SVDP/public/admin/fundprocess');
            
            // Username must consist of letters only
            //          must be between 5 and 20 characters
            $amount = $this->addElement('text', 'amount', array(
                               'filters'    => array('StringTrim',),
                              'validators' => array(
                                      'Alnum',),
                               'required'   => true,
                               'label'      => 'Amount:',
                             ));
           
            $submit = $this->addElement('submit','forgot', array(
                'required' => false,
                'ignore' => true,
                'label' => 'Submit',
            ));
    }
}