<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 3:44 PM
 */
namespace ProductShowcase\API;

use Menu\Form\MenuItem;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['product_showcase'] = array(
            'label' => 'PS_PRODUCT_SHOWCASE',
            'note' => '',
            'params' => array(array('route' => 'app/product-showcase'),),
        );
    }

    public function onTranslationDynamicEntityTypes(Event $e)
    {
        $fieldApi = getSM('fields_api');
        $fieldsTable = $fieldApi->init('product_showcase');
        $fields = getSM('fields_table')->getByEntityType('product_showcase')->toArray();
        $config = array(
            'entityType' => 'product_showcase_fields',
            'label' => 'Product Showcase Fields',
            'note' => 'translate product showcase dynamic fields',
            'table' => $fieldsTable,
            'pk' => 'entityId',
            'fields' => array()
        );

        $fieldsConfig = array();
        foreach ($fields as $f) {
            switch ($f['fieldType']) {
                case 'text':
                    $fieldsConfig[$f['fieldMachineName']] = array(
                        'label' => $f['fieldName'],
                        'column_type' => $fieldApi->getSqlType($f),
                        'type' => 'Text',
                    );
                    break;
                case 'long_text':
                    $fieldsConfig[$f['fieldMachineName']] = array(
                        'label' => $f['fieldName'],
                        'column_type' => $fieldApi->getSqlType($f),
                        'type' => 'Textarea',
                    );
                    break;
                default:
                    break;
            }
        }

        $config['fields'] = $fieldsConfig;
        $e->getTarget()->addToConfig('product_showcase_fields', $config);
    }
} 