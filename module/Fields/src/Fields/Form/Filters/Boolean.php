<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

use Zend\InputFilter\InputFilterProviderInterface;

class Boolean extends BaseFilter implements InputFilterProviderInterface
{
    protected $label = 'Boolean';
    protected $attributes = array(
        'id' => 'filter_Boolean',
        'name' => 'Zend\Filter\Boolean'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This filter changes a given input to be a BOOLEAN value. This is often useful when working with databases or when processing form values.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'casting',
            'options' => array(
                'label' => 'Casting',
                'description' => 'When this option is set to TRUE then any given input will be casted to boolean. This option defaults to TRUE.',
                'value_options' => array(
                    true => 'True',
                    false => 'False'
                )
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\MultiCheckbox',
            'name' => 'type',
            'options' => array(
                'label' => 'type',
                'description' => 'The type option sets the boolean type which should be used. Read the following for details.All other given values will return TRUE by default.',
                'value_options' => array(
                    \Zend\Filter\Boolean::TYPE_BOOLEAN => 'boolean: Returns a boolean value as is.',
                    \Zend\Filter\Boolean::TYPE_INTEGER => 'integer: Converts an integer 0 value to FALSE.',
                    \Zend\Filter\Boolean::TYPE_FLOAT => 'float: Converts a float 0.0 value to FALSE.',
                    \Zend\Filter\Boolean::TYPE_STRING => 'string: Converts an empty string ‘’ to FALSE.',
                    \Zend\Filter\Boolean::TYPE_ZERO_STRING => 'zero: Converts a string containing the single character zero (‘0’) to FALSE.',
                    \Zend\Filter\Boolean::TYPE_NULL => 'null: Converts a NULL value to FALSE.',
                    \Zend\Filter\Boolean::TYPE_PHP => 'php: Converts values according to PHP when casting them to BOOLEAN.',
                    \Zend\Filter\Boolean::TYPE_FALSE_STRING => 'false_string: Converts a string containing the word “false” to a boolean FALSE.',
                    \Zend\Filter\Boolean::TYPE_LOCALIZED => 'yes: Converts a localized string which contains the word “no” to FALSE.',
                    \Zend\Filter\Boolean::TYPE_ALL => 'all: Converts all above types to BOOLEAN.',
                )
            ),
            'attributes' => array()
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'type' => array(
                'name' => 'type',
                'required' => false,
                'allow_empty' => true
            )
        );
    }
}