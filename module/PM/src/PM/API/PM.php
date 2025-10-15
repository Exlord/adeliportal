<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:21 PM
 */

namespace PM\API;

use System\API\BaseAPI;
use Zend\Form\Element;

class PM extends BaseAPI
{
    const VIEW_ALL = 'route:admin/pm:all';
    const SEND_TO_MULTIPLE = 'route:admin/pm/new:multiple';
    const SEND_TO_ALL = 'route:admin/pm/new:all';
    const SEND = 'route:admin/pm/new';

    public static function removeFirstQuote($text)
    {
        if ($text && strlen($text)) {
            $start = strpos($text, '<blockquote>');
            if ($start > -1) {
                $end = strpos($text, '</blockquote>', $start + 12);
                if ($end > -1)
                    $text = substr_replace($text, '', $start, $end + 13 - $start);
            }
        }
        return $text;
    }
}