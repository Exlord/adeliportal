<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class UploadFile extends BaseValidator
{
    protected $label = 'UploadFile (FileUpload)';
    protected $attributes = array(
        'id' => 'validator_UploadFile',
        'name' => 'Zend\Validator\File\UploadFile'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks whether a single file has been uploaded via a form POST and will return descriptive messages for any upload errors (FileUploadField)');
    }
} 