<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/12/14
 * Time: 10:40 AM
 */

namespace Ads\View\Helper;


use System\View\Helper\BaseHelper;

class Categories extends BaseHelper
{
    public function __invoke($block)
    {
        $f_array = array();
        $t_array = array();
        $countState = 0;
        $countCity = 0;
        $baseTypeName = '';

        $type = $block->data[$block->type]['type'];
        $offset = strpos($type, '_');
        $type2 = substr($type, $offset + 1, strlen($type));
        $offset2 = strpos($type2, '_');
        $baseType = substr($type, $offset + 1, $offset2);
        $isRequest = substr($type2, $offset2 + 1, strlen($type2));

        $config = getSM('ads_api')->loadCache(null);
        if (isset($config[$baseType]))
            $baseTypeName = $config[$baseType]['name'];

        if (isset($block->data[$type]['table']['stateId']) && $block->data[$type]['table']['stateId']) {
            if (isset($block->data[$type]['table']['countryIdSelect']) && $block->data[$type]['table']['countryIdSelect'])
                $t_array['countryId'] = $block->data[$type]['table']['countryIdSelect'];
            if (isset($block->data[$type]['table']['countState']) && $block->data[$type]['table']['countState'])
                $countState = $block->data[$type]['table']['countState'];
        }

        if (isset($block->data[$type]['table']['cityId']) && $block->data[$type]['table']['cityId']) {
            if (isset($block->data[$type]['table']['stateIdSelect']) && $block->data[$type]['table']['stateIdSelect'])
                $t_array['stateId'] = $block->data[$type]['table']['stateIdSelect'];
            if (isset($block->data[$type]['table']['countCity']) && $block->data[$type]['table']['countCity'])
                $countCity = $block->data[$type]['table']['countCity'];
        }
        getSM()->get('fields_api')->init('ads_' . $baseType . '_' . $isRequest);


        if (isset($block->data[$type]['field']))
            foreach ($block->data[$type]['field'] as $field => $value) {
                if ($value) {
                    $fieldName = explode(',', $field);
                    $fieldData = getSM()->get('fields_api')->getFields(array($fieldName[0]));
                    if (is_array($fieldData))
                        foreach ($fieldData as $row)
                            if (isset($row['fieldConfigData']['select_field']))
                                foreach ($row['fieldConfigData']['select_field'] as $item)
                                    if (is_array($item))
                                        foreach ($item as $value)
                                            $f_array[$row['fieldMachineName']][$value['field_key']] = array(
                                                'name' => $row['fieldName'],
                                                'key' => $value['field_key'],
                                                'value' => $value['field_value'],
                                            );
                    // var_dump($item);
                    //  var_dump($fielddata);
                }
            }

        $block->blockId = 'ads_categories_' . $baseType . '_' . $isRequest . '_' . $block->id;
        $block->data["class"] .= ' ads_categories_' . $baseType . '_' . $isRequest . '_' . $block->id;

        $select = getSM('ads_table')->getAdsForBlock($t_array, $f_array, $baseType, $isRequest);
        return $this->view->render('ads/helper/categories', array(
            'select' => $select,
            'countState' => $countState,
            'countCity' => $countCity,
            'baseTypeName' => $baseTypeName,
            'baseType' => $baseType,

        ));
    }
} 