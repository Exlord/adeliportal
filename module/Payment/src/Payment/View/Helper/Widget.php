<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace Payment\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
    // protected $style = self::STYLE_10;
    protected $id = 'menu-widget';

    // public $cacheKey = 'gallery_widget';

    public function render()
    {
        $amount = getSM('transactions_api')->getTransactions(current_user()->id);
        return $this->view->render('payment/helper/widget', array('amount' => $amount));
    }

    public function isAllowed()
    {
        return isAllowed(\Page\Module::ADMIN_PAGE_WIDGET);
    }


}