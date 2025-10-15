<?php
/**
 * Created by PhpStorm.
 * User: Koushan
 * Date: 1/8/14
 * Time: 2:17 PM
 */

namespace OnlineOrders\API;


class API {
//for online orders
    function yesOrNo($int)
    {
        /* @var $basePath callable*/
        $basePath = getSM('viewhelpermanager')->get('basePath');
        if ($int == 1)
            return "<img src=" . $basePath() . "/images/Yes.png>";
        elseif ($int == 0)
            return "<img src=" . $basePath() . "/images/No.png>";
    }

    function checkOrText($int)
    {
        /* @var $basePath callable*/
        $basePath = getSM('viewhelpermanager')->get('basePath');
        if ($int == 1)
            return "<img src=" . $basePath() . "/images/textbox-icon.png>";
        elseif ($int == 0)
            return "<img src=" . $basePath() . "/images/checkbox-icon.png>";
    }
//end
} 