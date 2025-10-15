<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/15/14
 * Time: 3:10 PM
 */

namespace Application\API;


use System\API\BaseAPI;

class Breadcrumb extends BaseAPI
{
    private static $pages;


    public static function Init()
    {
        self::$pages = array(
            'Home' => url('app/front-page')
        );
    }

    public static function AddUriPage($label, $url)
    {
        self::$pages[$label] = $url;
    }

    public static function AddMvcPage($label, $route, $params = array(), $options = array())
    {
        self::$pages[$label] = url($route, $params, $options);
    }

    public static function getPages()
    {
        return self::$pages;
    }
} 