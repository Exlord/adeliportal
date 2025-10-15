<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class Alpha extends BaseFilter
{
    protected $label = 'Alpha';
    protected $attributes = array(
        'id' => 'filter_Alpha',
        'name' => 'Zend\I18n\Filter\Alpha'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('The Alpha filter can be used to return only alphabetic characters in the unicode “letter” category. All other characters are suppressed.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'allowWhiteSpace',
            'options' => array(
                'label' => 'Allow WhiteSpace',
                'description' => 'If set to true then whitespace characters are allowed. Otherwise they are suppressed. Default is “false” (whitespace is not allowed).'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'locale',
            'options' => array(
                'label' => 'Locale',
                'description' => 'The locale string used in identifying the characters to filter (locale name, e.g. en_US). If unset, it will use the default locale (Locale::getDefault()).'
            )
        ));
    }
} 