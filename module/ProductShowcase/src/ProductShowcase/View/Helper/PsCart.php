<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/11/13
 * Time: 8:55 AM
 */

namespace ProductShowcase\View\Helper;


use System\View\Helper\BaseHelper;

class PsCart extends BaseHelper
{
    public function __invoke($psIds = null, $type = 0)
    {
        if ($psIds) {
            $fieldsTable = getSM('fields_api')->init('product_showcase');
            $fields = getSM('fields_table')->getByEntityType(\ProductShowcase\Module::PS_ENTITY_TYPE)->toArray();
            $select = getSM('product_showcase_table')->getPsByIds(array_keys($psIds), $fieldsTable, $fields);
            $output = $this->view->render('product-showcase/helper/cart', array('select' => $select,'type'=>$type,'psIds'=>$psIds));
            return $output;
        }
    }
} 