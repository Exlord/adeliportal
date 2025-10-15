<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/10/13
 * Time: 10:37 AM
 */

namespace JQuery\API;

class JQuery
{
    public static function loadColorBox($view, $rel = 'colorbox')
    {
        $view->headScript()->appendFile($view->basePath() . '/js/jquery.colorbox-min.js');
        if (SYSTEM_LANG != 'en')
            $view->headScript()->appendFile($view->basePath() . '/js/i18n/jquery.colorbox-' . SYSTEM_LANG . '.js');
        $view->headLink()->appendStylesheet($view->basePath() . '/css/colorbox.css');

        $script = "$('.colorbox').colorbox({rel:'" . $rel . "'});";

        $view->inlineScript()->appendScript($script);
    }
} 