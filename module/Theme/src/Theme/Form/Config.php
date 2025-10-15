<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/28/14
 * Time: 1:41 PM
 */

namespace Theme\Form;


use System\Form\BaseForm;
use System\Form\Buttons;

class Config extends BaseForm
{

    public function __construct()
    {
        parent::__construct('theme_config');
        $this->setAttribute('class', 'normal-form ajax_submit');
        $this->setAction(url('admin/themes/config'));
    }

    protected function addElements()
    {
        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'fluid',
            'options' => array(
                'label' => 'Use Fluid Layout',
                'description' => 'system default is fixed layout(780 or 960 or 1200px), if Fluid is selected the container will resize with screen resolution'
            ),
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Textarea',
            'name' => 'layout',
            'options' => array(
                'label' => 'Site Theme Layout',
            ),
            'attributes' => array(
                'cols' => 50,
                'rows' => 10,
                'class' => 'left-align'
            )
        ));

        $this->add(new Buttons());
    }
}