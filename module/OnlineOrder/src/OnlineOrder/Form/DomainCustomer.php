<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace OnlineOrder\Form;

use Zend\Form\Fieldset;

class DomainCustomer extends Fieldset
{

    public function __construct()
    {
        parent::__construct('select-domain');
        $this->setAttribute('id', 'domain');
       // $this->setLabel('Domain Name');
        $this->attributes['class'] = 'collection-item well wel-sm';
        $this->setOptions(array('column_class' => 'col-md-4 col-sm-4'));


        $this->add(array(
            'name' => 'domainName',
            'type' => 'Zend\Form\Element\Text',
            'options'=>array(
           //   'label'=>'<span class=loading-domain ></span>',
            ),
            'attributes' => array(
                'class'=>'online-order-domain-input'
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
                'value' => t('Delete This Item'),
                'title' => t('Delete This Item'),
                'class' => 'btn btn-default drop_collection_item',
            ),
        ));
    }
}
