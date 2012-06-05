<?php

/**
 * Trivial form element that merely displays its label as HTML.
 */
class App_Form_Element_Note extends Zend_Form_Element_Xhtml
{

    /**
     * The view helper associated with this form element.
     *
     * @var string
     */
    public $helper = 'formNote';

    /**
     * Returns `true` if the specified value is valid and `false` if not. Since this element does
     * not accept user input, it shall always be considered valid.
     *
     * @param array $data
     * @param array|null $context
     * @return bool
     */
    public function isValid($data, $context = null)
    {
        return true;
    }
}
