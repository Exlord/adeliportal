<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Fields\Form;

use Zend\Form\Fieldset;

class ConstantField extends BaseConfig
{
    public function __construct()
    {
        parent::__construct('constant_field');
        $this->setAttribute('id', 'constant_settings');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'showLabel',
            'options' => array(
                'description' => 'display this fields label or not ?'
            )
        ));

//        $languages = getSM('language_table')->getArray(true);
//        foreach ($languages as $lSign => $lName) {
//            $langFieldset = new Fieldset($lSign);
//            $langFieldset->setLabel($lName);
//
//            $this->add($langFieldset);
//        }

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'value',
            'attributes' => array(
                'cols' => 40,
                'rows' => 5,
                'class' => 'hidden editor'
            )
        ));

    }
}
