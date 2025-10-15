<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/11/13
 * Time: 8:55 AM
 */

namespace RealEstate\View\Helper;


use System\View\Helper\BaseHelper;

class EstateList extends BaseHelper
{
    public function __invoke($params = '')
    {
        $output = $this->view->render('real-estate/helper/list', array('params' => $params));
        return $output;
    }
} 