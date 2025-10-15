<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Extension extends BaseValidator
{
    protected $label = 'Extension (FileUpload)';
    protected $attributes = array(
        'id' => 'validator_Extension',
        'name' => 'Zend\Validator\File\Extension'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('checks the extension of files (ex:jpg,png,rar). It will assert true when a given file has one the a defined extensions (FileUploadField)');

        $this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'Extension',
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