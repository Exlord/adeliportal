<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace Menu\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
    // protected $style = self::STYLE_10;
    protected $id = 'menu-widget';

    // public $cacheKey = 'gallery_widget';

    public function render()
    {
        $data = getSM('menu_table')->getCounts();
        return $this->view->render('menu/helper/widget', array('data' => $data));
    }

    public function isAllowed()
    {
        return isAllowed(\Page\Module::ADMIN_PAGE_WIDGET);
    }


}