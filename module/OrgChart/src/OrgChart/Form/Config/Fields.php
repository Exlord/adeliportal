<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/30/13
 * Time: 11:40 AM
 */

namespace OrgChart\Form\Config;


use Zend\Form\Fieldset;

class Fields extends Fieldset
{
    public function __construct($id, $fields)
    {
        parent::__construct($id);
        $this->setLabel($fields);

        /*$this->add(array(
            'name' => 'name',
            'type' => 'Zend\Form\Element\Hidden',
            'options' => array(
                'label' => 'name',
            ),
            'attributes'=>array(
                'value'=>$fields
            )
        ));*/

        $this->add(array(
            'type' => 'Zend\Form\Element\Checkbox',
            'name' => 'view',
            'options' => array(
                'label' => 'view',
            ),
        ));

       /* $this->add(array(
            'name' => 'order',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => ''
            ),
        ));*/

    }
} 