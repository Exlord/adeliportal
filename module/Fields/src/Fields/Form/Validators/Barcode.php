<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/20/13
 * Time: 9:22 AM
 */

namespace Fields\Form\Validators;

class Barcode extends BaseValidator
{
    protected $label = 'Barcode';
    protected $attributes = array(
        'id' => 'validator_Barcode',
        'name' => 'Zend\Validator\Barcode'
    );

    public function __construct()
    {
        parent::__construct();
        $this->setDescription('allows you to check if a given value can be represented as barcode.');

        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'adapter',
            'options' => array(
                'label' => 'Adapter',
                'description' => 'Sets the barcode adapter which will be used.',
                'value_options' => array(
                    'CODABAR' => 'CODABAR: Also known as Code-a-bar',
                    'CODE128' => 'CODE128: CODE128 is a high density barcode',
                    'CODE25' => 'CODE25: Often called “two of five” or “Code25 Industrial”',
                    'CODE25INTERLEAVED' => 'CODE25INTERLEAVED: Often called “Code 2 of 5 Interleaved”',
                    'CODE39' => 'CODE39: CODE39 is one of the oldest available codes',
                    //TODO add the rest of the adapters from http://framework.zend.com/manual/2.2/en/modules/zend.validator.set.html#barcode
                )
            ),
            'attributes'=>array(
                'class'=>'select2',
            ),
        ));


    }
} 