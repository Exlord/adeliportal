<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/19/14
 * Time: 9:37 AM
 */

namespace System\Form;

class CaptchaConfig extends BaseForm
{

    public function __construct()
    {
        parent::__construct('captcha_config');
        $this->setAction(url('admin/configs/captcha'));
        $this->setAttribute('class', 'ajax_submit');

    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => array(
                'label' => 'Captcha Type',
                'value_options' => array(
                    'math' => 'Math',
                    'image' => 'Image'
                ),
                'description' => 'Math:ask the user a mathematical question,<br/>Image:displays a word in a image ans user should type that word',
            ),
            'attributes' => array(
                'class' => 'select2'
            ),
        ));

        $this->add(new MathCaptcha());
        $this->add(new ImageCaptcha());

        $this->add(new Buttons('captcha_config'));
    }
}