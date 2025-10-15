<?php

namespace OnlineOrders\API;

class TreeFunction
{
    public $html = array('0'=>'-- Select --');

    public function baseTreeFunc($parentId,$groupsArray)
    {
        $this->list_groups_select_online_orders($parentId,$groupsArray);
        return $this->html;
    }

    public function list_groups_select_online_orders($parent, $menu_array)
    {
        $has_childs = false;
        foreach ($menu_array as $key => $value) {
            if ($value['groupParentId'] == $parent) {

                if ($has_childs === false) {
                    $has_childs = true;
                }
                $underLine = '';
                for ($i = 0; $i < $value['groupLevel']; $i++)
                    $underLine .= "_ ";

                $this->html[$value['id']]=$underLine . $value['groupName'];
                $this->list_groups_select_online_orders($key, $menu_array);
                echo '';
            }
        }
        if ($has_childs === true)
            echo '';

    }
}