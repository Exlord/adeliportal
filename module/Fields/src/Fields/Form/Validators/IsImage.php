<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class IsImage extends BaseValidator
{
    protected $label = 'Is Image (FileUpload)';
    protected $attributes = array(
        'id' => 'validator_IsImage',
        'name' => 'Zend\Validator\File\IsImage'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks if a file is an image, such as jpg or png (FileUploadField)');
    }
} 