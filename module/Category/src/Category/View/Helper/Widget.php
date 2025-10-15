<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace Category\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
   // protected $style = self::STYLE_10;
    protected $id = 'category-widget';

    public function render()
    {
        $data = getSM('category_table')->getCounts();
        return $this->view->render('category/helper/widget', array('data' => $data));
    }
}