<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/19/13
 * Time: 1:17 PM
 */

namespace Menu\Form\Fieldset;


use Zend\Form\Fieldset;

class Options extends Fieldset
{

    public function __construct()
    {
        parent::__construct('options');
        $this->setOptions(array('description' => ''));

        $this->add(array(
            'name' => 'showTitle',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Menu_form_ShowTitle',
                'value_options' => array(
                    1 => 'Yes',
                    0 => 'No',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'imgTitle',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Menu_form_imgTitle'
            ),
        ));

        $this->add(array(
            'name' => 'imgAlt',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Menu_form_imgAlt'
            ),
        ));

        $this->add(array(
            'name' => 'target',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'How to open the page?',
                'value_options' => array(
                    '_parent' => '_parent',
                    '_blank' => '_blank',
                    '_self' => '_self',
                    '_top' => '_top',
                ),
            ),
            'attributes'=>array(
                'class'=>'select2',
            )
        ));

        $this->add(array(
            'name' => 'nofollow',
            'type' => 'Zend\Form\Element\Checkbox',
            'options' => array(
                'label' => 'Is nofollow ?',
                'description'=> 'MENU_ITEM_NOFOLLOW_DESCRIPTION'
            ),
        ));

    }
} 