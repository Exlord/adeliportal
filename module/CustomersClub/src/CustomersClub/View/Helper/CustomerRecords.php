<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/7/2014
 * Time: 2:11 PM
 */

namespace CustomersClub\View\Helper;


use CustomersClub\Module;
use System\View\Helper\BaseHelper;
use Theme\API\Common;
use Theme\API\Table;

class CustomerRecords extends BaseHelper
{
    public function __invoke($userId)
    {
        if (isAllowed(Module::VIEW_CUSTOMER_RECORDS) || isCurrentUser($userId)) {
            $records = getSM('cc_api')->getRecords($userId);
            $temp = "<div class='col-md-4'><h4 class='page-header'>%s</h4>%s</div>";
            $content = '';
            foreach ($records as $name => $rows) {
                $table = Table::Table(array(), $rows, array('class' => 'table table-striped table-hover table-condensed '));
                $content .= sprintf($temp, t($name), $table);
            }

            return Common::Panel($content, t('Customer Records'));
        }
        return '';
    }
} 