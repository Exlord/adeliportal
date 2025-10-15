<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 1/18/14
 * Time: 1:31 PM
 */
namespace Notify\View\Helper;

use System\View\Helper\BaseHelper;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;

class Notifications extends BaseHelper implements EventManagerAwareInterface
{
    const EVENT_LOAD_NOTIFICATION_BAR = 'Notification.Load.Bar';
    /**
     * @var EventManagerInterface
     */
    protected $_eventManager;

    public $notifications = array();
    public $lastIndex = 0;

    public function __invoke($loadResources = true)
    {
        $this->getEventManager()->trigger(self::EVENT_LOAD_NOTIFICATION_BAR, $this);

        $uid = current_user()->id;
        $data = getSM('notify_table')->getAll(array('uId' => $uid, 'status' => 0), array('date DESC'), 10, $this->params()->fromQuery('page', 1));
        $this->insert($this->view->render('notify/helper/notify', array('data' => $data)));

        ksort($this->notifications);
        return $this->view->render('notify/helper/notification', array('items' => $this->notifications, 'loadResources' => $loadResources));
    }


    public function addMenu($icon, $title, $count, $subMenu = null, $link = '#', $badge_class = 'success', $order = null)
    {
        $badge = '';
        if ($count)
            $badge = "<span class='label label-{$badge_class} notify-badge'>{$count}</span>";
        if ($subMenu)
            $dropdown = "data-toggle='dropdown' class='dropdown-toggle'";
        else
            $dropdown = "class='ajax_page_load'";
        $html = "<li class='dropdown comment-menu'>
                         <a {$dropdown} href='{$link}' title='{$title}'>
                            <i class='$icon'></i>
                            {$badge}
                         </a>
                         {$subMenu}
                     </li>";


        $this->insert($html, $order);
    }

    public function addItem($text, $link = '#', $title = '', $order = null)
    {
        $html = "<li class='dropdown comment-menu'>
                         <a href='{$link}' title='{$title}'>
                            {$text}
                         </a>
                     </li>";

        $this->insert($html, $order);
    }

    public function insert($item, $at = null)
    {
        if ($at)
            $this->_insertAt($item, $at);
        else {
            $this->lastIndex = $this->_insertAt($item, $this->lastIndex);
            $this->lastIndex++;
        }
    }

    private function _insertAt($item, $index)
    {
        do {
            if (!isset($this->notifications[$index])) {
                $this->notifications[$index] = $item;
                return $index;
            }
            $index++;
        } while (true);
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $em
     * @return void|\Zend\EventManager\EventManagerInterface
     */
    public function setEventManager(EventManagerInterface $em)
    {
        $em->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->_eventManager = $em;
        return $this->_eventManager;
    }


    /**
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->_eventManager) {
            $this->setEventManager(new EventManager());
        }
        return $this->_eventManager;
    }
}