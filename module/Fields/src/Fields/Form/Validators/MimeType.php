<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class MimeType extends BaseValidator
{
    protected $label = 'MimeType (FileUpload)';
    protected $attributes = array(
        'id' => 'validator_MimeType',
        'name' => 'Zend\Validator\File\MimeType'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks the MIME type of files (ex:image/png). It will return true when a given file has one the a defined MIME types (FileUploadField)');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'MimeType',
            'options' => array(
                'label' => 'Extension',
                'count' => 0,
                'should_create_template' => true,
                'allow_add' => true,
                'target_element' => array(
                    'type' => 'Fields\Form\KeyField'
                ),
            ),
            'attributes' => array(
                'class' => 'collection-container'
            ),
        ));

        $this->add(array(
            'name' => 'add_more_select_option',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'title' => t('Add More Select Options'),
                'value' => t('Add More'),
                'class' => 'button add_button add_collection_item',
            ),
        ));
    }
} 