<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class ImageSize extends BaseValidator
{
    protected $label = 'Image Size (FileUpload)';
    protected $attributes = array(
        'id' => 'validator_ImageSize',
        'name' => 'Zend\Validator\File\ImageSize'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks the size of image files. Minimum and/or maximum dimensions can be set to validate against (FileUploadField)');

        $this->add(array(
            'name' => 'minWidth',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Minimum Width',
                'description' => 'dimensions are in pixel',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 5,
            )
        ));

        $this->add(array(
            'name' => 'minHeight',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Minimum Height',
                'description' => 'dimensions are in pixel',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 5,
            )
        ));

        $this->add(array(
            'name' => 'maxWidth',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum Width',
                'description' => 'dimensions are in pixel',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 5,
            )
        ));

        $this->add(array(
            'name' => 'maxHeight',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Maximum Height',
                'description' => 'dimensions are in pixel',
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-step' => 5,
            )
        ));
    }
} 