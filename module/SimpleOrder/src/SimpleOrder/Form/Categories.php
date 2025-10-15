<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/27/12
 * Time: 9:50 AM
 */

namespace SimpleOrder\Form;

use Zend\Form\Fieldset;
use Zend\InputFilter\InputFilterProviderInterface;
use Zend\InputFilter\InputProviderInterface;
use \SimpleOrder\Model\SimpleOrderTable;

class Categories extends Fieldset implements InputFilterProviderInterface
{
    public function __construct()
    {
        $quantity = array();
        foreach (SimpleOrderTable::$quantity as $key => $val)
            $quantity[$key] = t($val);
        $categoryArray = array(0 => t('-- Select --'));
        $category = getSM('category_item_table')->getItemsFirstLevelByMachineName('simpleOrder');
        foreach ($category as $row)
            $categoryArray[$row['id']] = $row['itemName'];
        parent::__construct('select_order_category');
        $this->setAttribute('id', 'orderCategory');
        $this->setLabel('Sub Categories');
        $this->attributes['class'] = 'inline-collection collection-item';


        $this->add(array(
            'name' => 'categoryItem',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'SIMPLE_ORDER_PRODUCT',
                'value_options' => $categoryArray
            ),
            'attributes' => array(
                'class' => 'category-item'
            )
        ));


        $this->add(array(
            'name' => 'subCategoryItem',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'SIMPLE_ORDER_SUB_CATEGORY',
                'value_options' => array(0 => t('-- Select --'))
            ),
            'attributes' => array(
                'class' => 'sub-category-item'
            )
        ));

        $this->add(array(
            'name' => 'quantity',
            'type' => 'Zend\Form\Element\Select',
            'options' => array(
                'label' => 'Type',
                'value_options' => $quantity
            ),
            'attributes' => array(
                'class' => 'sub-category-quantity'
            )
        ));

        $this->add(array(
            'name' => 'count',
            'type' => 'Zend\Form\Element\Text',
            'options' => array(
                'label' => 'simpleOrder_label_quantity'
            ),
            'attributes' => array(
                'class' => 'sub-category-count'
            )
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
                'title' => 'Delete This Item',
                'class' => 'button icon_button delete_button drop_collection_item',
            ),
        ));
    }

    /**
     * Should return an array specification compatible with
     * {@link Zend\InputFilter\Factory::createInputFilter()}.
     *
     * @return array
     */
    public function getInputFilterSpecification()
    {
        return array(
            'subCategoryItem' => array(
                'required' => false,
                'allow_empty' => true,
            ),
        );
    }
}
