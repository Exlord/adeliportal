<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/3/13
 * Time: 8:49 AM
 */

namespace FormsManager\API;


use System\API\BaseAPI;
use System\IO\Directory;

class Form extends BaseAPI
{
    const SIMPLE_FORM = 1;
    const CUSTOM_FORM = 2;

    public static $FormTypes = array(
//        self::SIMPLE_FORM => 'Simple Form',
        self::CUSTOM_FORM => 'Custom Form',
    );

    public static $HtmlClassForFormType = array(
        self::SIMPLE_FORM => 'simple-dynamic-form',
        self::CUSTOM_FORM => 'custom-dynamic-form',
    );


    public static function getAvailableTemplates()
    {
        $cacheKey = "forms_template_list";
        if (!$templates = getCacheItem($cacheKey)) {
            $publicTemplates = Directory::getFiles(ROOT . '/module/FormsManager/view/forms-manager/template');
            $privateTemplates = Directory::getFiles(PUBLIC_PATH . TEMPLATE_PATH . '/forms-manager/template');
            if ($privateTemplates)
                $publicTemplates = array_merge($publicTemplates, $privateTemplates);

            $templates = array();
            foreach ($publicTemplates as $temp) {
                $name = array_shift(explode('.', $temp));
                $templates[$name] = $name;
            }

            setCacheItem($cacheKey, $templates);
        }
        return $templates;
    }
} 