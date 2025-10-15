<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 10/20/13
 * Time: 2:46 PM
 */

namespace Application\View\Helper;


use System\View\Helper\BaseHelper;

class Body extends BaseHelper
{
    public $class = array();

    public function __invoke()
    {
        return $this;
    }

    public function getSiteTitle()
    {
        $config = getSM('config_table')->getByVarName('system_config')->varValue;
        $siteTitle = '';
        if (isset($config['siteTitle']) && $config['siteTitle'])
            $siteTitle = $config['siteTitle'];
        if (!empty($siteTitle))
            return '<h2 class="system-site-title">' . $siteTitle . '</h2>';
        else
            return '';
    }

    public function getSiteSubTitle()
    {
        $config = getSM('config_table')->getByVarName('system_config')->varValue;
        $siteSubTitle = '';
        if (isset($config['siteSubTitle']) && $config['siteSubTitle'])
            $siteSubTitle = $config['siteSubTitle'];
        if (!empty($siteSubTitle))
            return '<h2 class="system-site-sub-title">' . $siteSubTitle . '</h2>';
        else
            return '';
    }
}