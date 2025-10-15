<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace Payment\Form;

use Zend\Form\Fieldset;

class CCPointsConfig extends Fieldset
{
    public function __construct()
    {
        parent::__construct('cc_points_config');
        $this->setLabel('');
        $this->attributes['class'] = 'collection-item well well-sm';
        $this->options['column_class'] = 'col-md-4';
        $this->add(array(
            'name' => 'from',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'From',
                'description' => 'payment amounts starting from this value',
            ),
            'attributes' => array(
                'placeholder' => t('Payments From')
            ),
        ));

        $this->add(array(
            'name' => 'to',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'To',
                'description' => 'payment values ending to this value',
            ),
            'attributes' => array(
                'placeholder' => t('Payment To')
            ),
        ));

        $this->add(array(
            'name' => 'points',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Points',
                'description' => 'how many points for payment amounts in this range',
            ),
            'attributes' => array(
                'placeholder' => t('Points')
            ),
        ));


        $this->add(array(
            'name' => 'drop_collection_item',
            'options' => array(
                'label' => '',
                'description' => '',
            ),
            'attributes' => array(
                'type' => 'button',
                'value' => 'Delete This Item',
                'title' => t('Delete This Item'),
                'class' => 'btn btn-default drop_collection_item',
            ),
        ));

    }
}
