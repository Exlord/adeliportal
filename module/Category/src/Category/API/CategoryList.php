<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/3/13
 * Time: 3:50 PM
 */
namespace Category\API;

use System\API\BaseAPI;

class CategoryList extends BaseAPI
{
    public function createCatItemList($dataArray,$options,$category,$countLevel,$parentId)
    {
        $dataArray = $this->createArrayAndResize($dataArray, $options, $category, $countLevel);
        $parents = array();
        foreach ($dataArray as $row) {
            $parents[$row['parentId']][] = $row;
        }
        $items = array();
        $this->__sortItem($parents, $parentId, $items);
        $html = $this->ItemList($items);
        return $html;
    }

    private function createArrayAndResize($select, $options, $category, $countLevel)
    {
        $data = array();
        while ($countLevel > 0) {
            if (isset($select[$countLevel])) {
                foreach ($select[$countLevel] as $row) {
                    $imageUrl = '';
                    if (isset($row->image)) {
                        $imageUrl = $row->image;
                        if ($row->image) {
                            if ($options['imageWidth'] && $options['imageHeight']) {
                                if ($options['resizeType'] == 'fix')
                                    $imageUrl = getThumbnail()->resize($row->image, $options['imageWidth'], $options['imageHeight']); //resize image
                                elseif ($options['resizeType'] == 'relative')
                                    $imageUrl = getThumbnail()->thumbnail($row->image, $options['imageWidth'], $options['imageHeight']); //resize image
                            }
                        }
                    }
                    $data[] = array(
                        'id' => $row->id,
                        'title' => $row->itemName,
                        'image' => $imageUrl,
                        'parentId' => $row->parentId,
                        'countChild' => $row->countChild,
                        'catId'=>$category->id,
                    );
                }
            }
            $countLevel--;
        }
        $dataObject = new \stdClass();
        $dataObject->data = $data;

        getSM('category_item_api')->UrlGenerate($category->catMachineName, $dataObject);
        return $dataObject->data;
    }

    private function __sortItem(&$parents, $pId, &$items)
    {
        if (isset($parents[$pId])) {
            foreach ($parents[$pId] as $item) {
                $items[$item['id']]['data'] = $item;
                $this->__sortItem($parents, $item['id'], $items[$item['id']]['children']);
            }
        }
    }

    private function ItemList(array $items, $title = '')
    {
        $output = '<div class="item-list box_categories_list">';
        if (isset($title) && $title !== '') {
            $output .= '<h3>' . $title . '</h3>';
        }

        if (!empty($items)) {
            $output .= "<ul class='nav nav-pills nav-stacked'>";
            $num_items = count($items);
            $i = 0;
            foreach ($items as $item) {
                $children = array();
                $data = '';
                $i++;
                if (is_array($item)) {
                    foreach ($item as $key => $value) {
                        if ($key == 'data') {
                            $htmlCount = '';
                            if($value['countChild']>0)
                                $htmlCount = '<span class="badge pull-left">'.$value['countChild'].'</span>';
                            $data = '<a href="'.$value['url'].'" >'.$htmlCount.$value['title'].'</a>';
                        } elseif ($key == 'children') {
                            $children = $value;
                        }
                    }
                }
                if (count($children) > 0) {
                    // Render nested list.
                    $data .= self::ItemList($children);
                }
                if ($i == 1) {
                    //    $attributes['class'][] = 'first';
                }
                if ($i == $num_items) {
                    //   $attributes['class'][] = 'last';
                }
                $output .= '<li>' . $data . "</li>\n";
            }
            $output .= "</ul>";
        }
        $output .= '</div>';
        return $output;
    }
}