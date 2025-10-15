<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/10/13
 * Time: 11:06 AM
 */

namespace RealEstate\API;


class RealEstate {
    /**
     * @param $roleId
     * @return bool
     */
     public static function isRealEstateAgent($roleId)
    {
        if(!$roleId)
            return false;
        if (!is_array($roleId)) {
            $roleId[] = $roleId;
        }
        $config = getSM()->get('config_table')->getByVarName('real_estate_config');
        $roles = $config->varValue['agentUserRole'];
        foreach ($roleId as $role) {
            if (in_array($role, $roleId))
                return true;
        }
        return false;
    }


    public function getCategories()
    {
        $dataArray = array();
        $selectCat = getSM('category_table')->getAll(array('catMachineName' => 'estate_type'))->current();
        if(isset($selectCat->id))
        {
            $dataArray = getSM('category_item_table')->getItemsTree($selectCat->id);
        }
        return $dataArray;
    }

} 