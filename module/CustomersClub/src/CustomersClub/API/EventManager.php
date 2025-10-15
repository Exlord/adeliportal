<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 12:52 PM
 */

namespace CustomersClub\API;


use Zend\EventManager\Event;

class EventManager
{
    public function onLoadNotificationBar(Event $e)
    {
        $count = (int)getSM('points_total_table')->getMyPoints(current_user()->id);

        $icon = 'glyphicon glyphicon-heart';

        $badge_class = '';
        if ($count == 0) {
            $icon .= ' text-muted';
            $badge = '';
        } elseif ($count > 0) {
            $badge_class = 'success';
            $badge = '+';
        } else {
            $badge_class = 'danger';
            $badge = '+';
        }
        $title = sprintf(t('You have a total of %s points'), $count);

        $link = url('admin/customers-club/my-points');

        if ($count)
            $badge = "<span class='label label-{$badge_class}' dir='ltr'>{$count}</span>";


        $html = "<li class='dropdown points-menu'>
                         <a class='ajax_page_load' href='{$link}' title='{$title}'>
                             {$badge}
                            <i class='$icon fa-lg'></i>
                         </a>
                     </li>";


        $e->getTarget()->insert($html, -1000);
    }
} 