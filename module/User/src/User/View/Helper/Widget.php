<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace User\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
//    protected $style = self::STYLE_1;
    protected $id = 'user-widget';

   // public $cacheKey = 'users_widget';

    public function render()
    {
        $data = getSM('role_table')->getCounts();
        return $this->view->render('user/user/widget', array('data' => $data));
    }

    public function isAllowed()
    {
       return isAllowed(\User\Module::ADMIN_USER_WIDGET);
    }


}