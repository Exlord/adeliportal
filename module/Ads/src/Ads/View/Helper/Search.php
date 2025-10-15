<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/12/14
 * Time: 10:40 AM
 */

namespace Ads\View\Helper;


use System\View\Helper\BaseHelper;

class Search extends BaseHelper
{
    public function __invoke($block)
    {
        $type = $block->data[$block->type]['type'];
        $offset = strpos($type, '_');
        $type2 = substr($type, $offset + 1, strlen($type));
        $offset2 = strpos($type2, '_');
        $baseType = substr($type, $offset + 1, $offset2);
        $isRequest = substr($type2, $offset2 + 1, strlen($type2));

        $block->blockId = 'ads_search' . '_' . $baseType . '_' . $isRequest . '_' . $block->id;
        $block->data["class"] .= " ads_search_" . $baseType . "_" . $isRequest;
        $static_fields_data = array();

        $catId = 0;
        if ($baseType) {
            $selectCate = getSM('category_table')->getByMachineName('ads_category_' . $baseType);
            if ($selectCate)
                $catId = $selectCate->id;
        }

        $request = getSM('Request');
        $data = array_merge_recursive($request->getQuery()->toArray(), $request->getPost()->toArray());
        $form = new \Ads\Form\Search($block, $baseType, $isRequest);
        $form->setData($data);
        $form->isValid();

        return $this->view->render('ads/helper/search', array(
            'block' => $block,
            'static_fields_data' => $static_fields_data,
            'form' => $form,
            'baseType' => $baseType,
            'catId' => $catId,
        ));
    }
} 