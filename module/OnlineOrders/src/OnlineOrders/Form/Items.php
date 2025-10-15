<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/21/12
 * Time: 2:14 PM
 */

namespace OnlineOrders\Form;

use System\Form\BaseForm;
use Zend\Captcha;
use Zend\Form\Element;
use Zend\Form\Fieldset;
use Zend\Form\Form;
use Zend\Filter;

class Items extends BaseForm
{
    private $groupId;

    public function __construct($groupId)
    {
        $this->groupId = $groupId;
        parent::__construct('items_form');
        $this->setAttributes(array(
            'class', 'normal-form',
            'action' => url('admin/online-orders/item-select')
        ));
    }

    protected function addElements()
    {

        $this->add(array(
            'name' => 'id',
            'type' => 'Zend\Form\Element\Hidden',
        ));

        $text0 = new Element\Textarea('itemName');
        $text0->setLabel("Item Name");
        $text0->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text0);


        $text = new Element\Textarea('itemDesc');
        $text->setLabel("Description");
        $text->setAttributes(array(
            'cols' => '100',
            'rows' => '4',
        ));
        $this->add($text);

        $text1 = new Element\Textarea('itemDescMore');
        $text1->setLabel("More Description");
        $text1->setAttributes(array(
            'cols' => '80',
            'rows' => '3',
            'class' => 'editor-edit-items'
        ));
        $this->add($text1);


        $this->add(array(
            'name' => 'itemPrice',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Price'
            ),
        ));


        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'itemType',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                     '0' => 'Checkbox',
                     '1' => 'Text',
                ),
            ),
            'attributes'=>array(
                'class' => 'item-type-radio',
            )
        ));

        $this->add(array(
            'type' => 'Zend\Form\Element\Radio',
            'name' => 'itemActive',
            'options' => array(
                'label' => 'You are allowed to choose ?',
                'value_options' => array(
                    '0' => 'Yes',
                    '1' => 'No',
                ),
            )
        ));


        $this->add(array(
            'name' => 'itemPosition',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'Position'
            ),
        ));

        $groupId = new Element\Hidden('groupId');
        $groupId->setValue($this->groupId);
        $this->add($groupId);


        $this->add(array(
            'type' => 'Zend\Form\Element\Csrf',
            'name' => 'csrf_items_form'
        ));

        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-create',
            'attributes' => array(
                'value' => 'Save',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-edit',
            'attributes' => array(
                'value' => 'Edit',
                'class' => 'button',
            )
        ));
        $this->add(array(
            'type' => 'submit',
            'name' => 'submit-delete',
            'attributes' => array(
                'value' => 'Delete',
                'class' => 'button',
            )
        ));


    }


    protected function addInputFilters()
    {

    }

}
