<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class StringToUpper extends BaseFilter
{
    protected $label = 'StringToUpper';
    protected $attributes = array(
        'id' => 'filter_StringToUpper',
        'name' => 'Zend\Filter\StringToUpper'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This filter converts any input to be uppercased.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'encoding',
            'options' => array(
                'label' => 'Encoding',
                'description' => 'This option can be used to set an encoding which has to be used.'
            )
        ));
    }
} 