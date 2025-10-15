<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 12:41 PM
 */

namespace Category\API;


use Components\Form\NewBlock;
use Zend\Form\Fieldset;

class EventManager
{
    public function onLoadBlockConfigs($e)
    {
        /* @var $form NewBlock */
        $form = $e->getParam('form');
        $type = $e->getParam('type');
        $dataFieldset = $form->get('data');
        if ($type == 'category_list_block') {
            $form->extraScripts[] = "/js/category-list-block.js";
            $allCat = getSM('category_table')->getAllArray();
            $allCatItem = getSM('category_item_table')->getAllArray();
            $fiedlset = new Fieldset($type);
            $dataFieldset->setLabel('New Category List Block Settings');
            $dataFieldset->setAttribute('class', 'category-list-block');
            $dataFieldset->add($fiedlset);

            $fiedlset->add(array(
                'name' => 'catId',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'category ?',
                    'value_options' => $allCat
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            /*$fiedlset->add(array(
                'name' => 'itemId',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Category Items ?',
                    'value_options' => $allCatItem,
                    'disable_inarray_validator' => true,
                ),
                'attributes' => array(
                    'class' => 'select2',
                    'multiple' => 'multiple',
                    'data-url' => url('admin/category/get-item-list'),
                    'empty_option' => '-- Select --',

                )
            ));*/

            $fiedlset->add(array(
                'name' => 'countLevel',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Count Level ?',
                    'description' => ''
                ),
                'attributes' => array(
                    'value' => 2
                ),
            ));

            $fiedlset->add(array(
                'name' => 'imageWidth',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Image width ?',
                    'description' => ''
                ),
                'attributes' => array(),
            ));

            $fiedlset->add(array(
                'name' => 'imageHeight',
                'type' => 'Zend\Form\Element\Text',
                'options' => array(
                    'label' => 'Image height ?',
                    'description' => ''
                ),
                'attributes' => array(),
            ));

            $fiedlset->add(array(
                'name' => 'resizeType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Resize Type ?',
                    'value_options' => array(
                        'fix' => 'fix',
                        'relative' => 'relative',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            $fiedlset->add(array(
                'name' => 'titleType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Title Type ?',
                    'value_options' => array(
                        'normal' => 'Normal',
                        'caption' => 'Caption',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            $fiedlset->add(array(
                'name' => 'positionType',
                'type' => 'Zend\Form\Element\Select',
                'options' => array(
                    'label' => 'Type ?',
                    'value_options' => array(
                        'vertical' => 'Vertical',
                        'horizontal' => 'Horizontal',
                    ),
                ),
                'attributes' => array(
                    'class' => 'select2',
                )
            ));

            $fiedlset->add(array(
                'name' => 'type',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => 'category_list',
                )
            ));

            $fiedlset->add(array(
                'name' => 'url',
                'type' => 'Zend\Form\Element\Hidden',
                'attributes' => array(
                    'value' => url('admin/category/get-item-list'),
                )
            ));
        }
    }
} 