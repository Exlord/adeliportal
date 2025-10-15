<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 12:44 PM
 */
namespace Comment\API;

use Zend\EventManager\Event;

class EventManager
{
    public function onLoadNotificationBar(Event $e)
    {
        $count = (int)getSM('comment_table')->getUnapprovedCount();
        $icon = 'glyphicon glyphicon-comment fa-lg';
        if ($count)
            $title = sprintf(t('There are %s comments waiting your approval.'), $count);
        else {
            $title = t('There are no unapproved comments.');
            $icon .= ' text-muted';
        }
        $e->getTarget()->addMenu($icon, $title, $count);
    }
} 