<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/20/14
 * Time: 9:19 AM
 */

namespace System\Form;


use Zend\Form\Fieldset;

class ImageCaptcha extends Fieldset
{

    public function __construct()
    {
        parent::__construct('image');
        $this->setLabel('Image Captcha');

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'numbersOnly',
            'options' => array(
                'label' => 'Numbers Only',
                'description' => 'if checked the generated word will contain only numbers and no letters'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'DotNoiseLevel',
            'options' => array(
                'label' => 'Dot Noise Level',
                'description' => 'Number of noise dots on image (default:100)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-max' => 200,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'lineNoiseLevel',
            'options' => array(
                'label' => 'Line Noise Level',
                'description' => 'Number of noise lines on image (default:5)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 0,
                'data-max' => 200,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'Wordlen',
            'options' => array(
                'label' => 'Word Length',
                'description' => 'Length of the word to generate (default:8)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 4,
                'data-max' => 15,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'FontSize',
            'options' => array(
                'label' => 'Font Size',
                'description' => '(default:24)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 10,
                'data-max' => 30,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'Width',
            'options' => array(
                'label' => 'Width',
                'description' => 'Image width (default:200)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 20,
                'data-max' => 300,
                'data-step' => 1,
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'Height',
            'options' => array(
                'label' => 'Height',
                'description' => 'Image Height (default:50)'
            ),
            'attributes' => array(
                'class' => 'spinner',
                'data-min' => 20,
                'data-max' => 150,
                'data-step' => 1,
            )
        ));
    }
}