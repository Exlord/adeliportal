<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 8/20/14
 * Time: 10:37 AM
 */

namespace User\Form\Config;


use User\API\User;
use Zend\Form\Fieldset;

class FieldsAccess extends Fieldset
{
    public function __construct($fields)
    {
        parent::__construct('fields_access');
        $this->setLabel('Fields Access Level');

        $staticFields = User::$STATIC_FIELDS;

        foreach ($staticFields as $id => $field) {
            $this->add(array(
                'type' => 'Select',
                'name' => $id,
                'options' => array(
                    'label' => t($field),
                    'value_options' => array(
                        'private' => 'Private',
                        'members' => 'Site Members',
                        'public' => 'Public',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                    'data-field-id' => $id
                )
            ));
        }

        if ($fields) {
            foreach ($fields as $id => $field) {
                $this->add(array(
                    'type' => 'Select',
                    'name' => $id,
                    'options' => array(
                        'label' => $field,
                        'value_options' => array(
                            'private' => 'Private',
                            'members' => 'Site Members',
                            'public' => 'Public',
                        ),
                    ),
                    'attributes' => array(
                        'class' => 'select2',
                        'data-field-id' => $id
                    )
                ));
            }
        }
    }
} 