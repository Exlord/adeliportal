<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:49 PM
 */

namespace RealEstate\View\Helper;

use \Application\View\Helper\Widget as BaseWidget;

class Widget extends BaseWidget
{
    protected $width = self::WIDTH_25;
    protected $style = self::STYLE_10;
    protected $id = 'real-estate-widget';

   // public $cacheKey = 'real_estate_widget';

    public function render()
    {
        $data = getSM('real_estate_table')->getCounts();
//        $data['count']['special'] = getSM('real_estate_table')->getAllSpecial();
//        $data['count']['requested']= getSM('real_estate_table')->getAllRequestedEstate();
        return $this->view->render('real-estate/helper/widget', array('data' => $data));
    }

    public function isAllowed()
    {
        return isAllowed(\RealEstate\Module::ADMIN_REAL_ESTATE_WIDGET);
    }


}