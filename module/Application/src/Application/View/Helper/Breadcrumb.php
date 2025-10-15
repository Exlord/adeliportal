<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/15/14
 * Time: 3:09 PM
 */

namespace Application\View\Helper;


use Menu\Navigation\Service\DynamicNavigationFactory;
use System\View\Helper\BaseHelper;
use Theme\API\Common;

class Breadcrumb extends BaseHelper
{
    public function __invoke($minLength = 1)
    {
        $nav = \Application\API\Breadcrumb::getPages();

        $items = array();
        $count = count($nav);
        if ($count >= $minLength) {
            $i = 1;
            foreach ($nav as $label => $url) {
                $attr = array();
                if ($count == $i)
                    $attr['class'] = 'active';
                $items[] = Common::Link(t($label), $url, $attr);
                $i++;
            }

            return Common::ItemList($items);
        }
        return '';
    }
} 