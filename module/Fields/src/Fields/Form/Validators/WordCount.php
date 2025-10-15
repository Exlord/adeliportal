<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class WordCount extends BaseValidator
{
    protected $label = 'WordCount';
    protected $attributes = array(
        'id' => 'validator_WordCount',
        'name' => 'Zend\Validator\File\WordCount'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks for the number of words within a file.');

        $this->add(array(
            'name' => 'min',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Minimum',
                'description' => '',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 5,
            )
        ));

        $this->add(array(
            'name' => 'max',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 5,
            )
        ));
    }
} 