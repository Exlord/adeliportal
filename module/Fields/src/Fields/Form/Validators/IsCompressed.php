<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class IsCompressed extends BaseValidator
{
    protected $label = 'Is Compressed (FileUpload)';
    protected $attributes = array(
        'id' => 'validator_IsCompressed',
        'name' => 'Zend\Validator\File\IsCompressed'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks if a file is a compressed archive, such as zip or gzip (FileUploadField)');
    }
} 