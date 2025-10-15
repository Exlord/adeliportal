<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Size extends BaseValidator
{
    protected $label = 'Image Size (FileUpload)';
    protected $attributes = array(
        'id' => 'validator_Size',
        'name' => 'Zend\Validator\File\Size'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks for the size of a file (FileUploadField), The integer number of bytes, or a string in SI notation (ie. 1kB, 2MB, 0.2GB) The accepted SI notation units are: kB, MB, GB, TB, PB, and EB. All sizes are converted using 1024 as the base value (ie. 1kB == 1024 bytes, 1MB == 1024kB)');

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