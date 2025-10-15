<?php
namespace RealEstate\View\Helper;
use System\View\Helper\BaseHelper;

/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/1/13
 * Time: 8:36 PM
 */

class EstateTypeBlock extends BaseHelper
{
    public function __invoke()
    {
        $category_table = getSM()->get('category_item_table');
        $estateType = $category_table->getItemsTreeByMachineName('estate_type');
        $list = array();
        $temp = "<a href='%s'>%s</a>";
        foreach ($estateType as $key => $value) {
            $list[] = sprintf(
                $temp,
                url('app/real-estate', array(), array('query' => array('filter_estateType' => $key, 'search' => t('Search')))),
                $value
            );
        }
        return array('block_title' => t('Real Estate Types'), 'block_content' => $this->getView()->htmlList($list, false, array(), false));
    }

}