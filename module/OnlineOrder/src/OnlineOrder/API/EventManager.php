<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 4:23 PM
 */

namespace OnlineOrder\API;


use Menu\Form\MenuItem;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');
        $form->menuTypes['online-order'] = array(
            'label' => 'Online Order',
            'note' => 'Allows your visitors to order a site for themselves online.',
            'params' => array(array('route' => 'app/online-order')),
        );
    }
} 