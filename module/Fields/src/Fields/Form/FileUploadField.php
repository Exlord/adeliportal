<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

class FileUploadField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('file_upload_field');
        $this->setAttribute('id', 'fileUpload_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'alt',
            'options' => array(
                'label' => 'Alt (Only for image files)',
                'description' => 'provide a text field to enter a alt text for this file field'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'title',
            'options' => array(
                'label' => 'Title (Only for image files)',
                'description' => 'provide a text field to enter a Title for this file field'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'link',
            'options' => array(
                'label' => 'Link (Only for image files)',
                'description' => 'provide a text field to enter a url for this file field ,the image will be linked to the given url'
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'thumbW',
            'options' => array(
                'label' => 'Thumbnail Width (Only for image files)',
                'description' => ''
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'thumbH',
            'options' => array(
                'label' => 'Thumbnail Height (Only for image files)',
                'description' => ''
            )
        ));
    }
}
