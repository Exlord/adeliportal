<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace Gallery\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
    protected $style = self::STYLE_10;
    protected $id = 'gallery-widget';

   // public $cacheKey = 'gallery_widget';

    public function render()
    {
        $data = getSM('order_banner_table')->getCounts();
        return $this->view->render('gallery/gallery/widget', array('data' => $data));
    }
}