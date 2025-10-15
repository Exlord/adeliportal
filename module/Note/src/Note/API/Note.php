<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/3/14
 * Time: 2:53 PM
 */

namespace Note\API;


use System\API\BaseAPI;

class Note extends BaseAPI
{
    const EVENT_VISIBILITY = 'Note.Event.Visibility';
    const EVENT_VISIBILITY_FILTER = 'Note.Event.Visibility.Filter';
    public static $visibility = array(
//        'private' => 'NOTE_VISIBILITY_PRIVATE',
        'auth' => 'NOTE_VISIBILITY_AUTH',
        'public' => 'NOTE_VISIBILITY_PUBLIC',
    );

    public function setVisibility($entityType)
    {
        $this->getEventManager()->trigger(self::EVENT_VISIBILITY, null, array('entityType' => $entityType));
    }

    public function setVisibilityFilter($entityType, $entityId, &$where, &$select)
    {
        $this->getEventManager()->trigger(self::EVENT_VISIBILITY_FILTER, null,
            array('entityType' => $entityType, 'entityId' => $entityId, 'where' => &$where, 'select' => &$select));
    }
}