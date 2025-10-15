<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/19/13
 * Time: 1:17 PM
 */

namespace Menu\Form\Fieldset;


use Zend\Form\Fieldset;

class Mega extends Fieldset
{

    public function __construct()
    {
        parent::__construct('megaMenu');
//        $this->setLabel('Mega Menu');
        $this->setOptions(array('description' => 'setting these options will show this items sub-menu items as a MegaMenu item.'));

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'isMega',
            'options' => array(
                'label' => 'Is mega menu ?',
                'description' => 'Do you want this items sub menus to be displayed as a mega menu ?'
            ),
            'attributes' => array()
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Text',
            'name' => 'columns',
            'options' => array(
                'label' => 'Columns',
                'description' => 'how many columns should this mega menu item have ?'
            ),
            'attributes' => array()
        ));
    }
} 