<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Filters;

class StringToLower extends BaseFilter
{
    protected $label = 'StringToLower';
    protected $attributes = array(
        'id' => 'filter_StringToLower',
        'name' => 'Zend\Filter\StringToLower'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('This filter converts any input to be lowercased.');

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