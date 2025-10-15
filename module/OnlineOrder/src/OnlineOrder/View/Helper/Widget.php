<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace OnlineOrder\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
    protected $style = self::STYLE_10;
    protected $id = 'online-order-widget';

   // public $cacheKey = 'online-order_widget';

    public function render()
    {
        $data = getSM('customer_table')->getCounts();
        return $this->view->render('online-order/online-order/widget', array('data' => $data));
    }
}