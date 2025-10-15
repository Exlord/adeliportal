<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 3:41 PM
 */

namespace PM\API;


use Zend\EventManager\Event;

class EventManager
{
    public function onLoadNotificationBar(Event $e)
    {
        $count = (int)getSM('pm_table')->getUnreadCount();
        $icon = 'glyphicon glyphicon-envelope fa-lg';
        if ($count) {
            if ($count > 1)
                $title = sprintf(t('You have %s unread private messages.'), $count);
            else
                $title = t('You have 1 unread private message.');
        } else {
            $title = t('You have no unread private messages.');
            $icon .= ' text-muted';
        }
        $e->getTarget()->addMenu($icon, $title, $count, null, url('admin/pm'));
    }
} 