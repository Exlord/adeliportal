<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class StringTrim extends BaseFilter
{
    protected $label = 'StringTrim';
    protected $attributes = array(
        'id' => 'filter_StringTrim',
        'name' => 'Zend\Filter\StringTrim'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This filter modifies a given string such that certain characters are removed from the beginning and end.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'charlist',
            'options' => array(
                'label' => 'Char list',
                'description' => 'List of characters to remove from the beginning and end of the string. If this is not set or is null, the default behavior will be invoked, which is to remove only whitespace from the beginning and end of the string.'
            )
        ));
    }
} 