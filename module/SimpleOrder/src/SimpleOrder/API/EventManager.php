<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 4:10 PM
 */
namespace SimpleOrder\API;

use Menu\Form\MenuItem;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['simple-order'] = array(
            'label' => 'simpleOrder_new_order_form',
            'note' => 'simpleOrder_new_page_title',
            'params' => array(array('route' => 'app/simple-order')),
        );

        $form->menuTypes['step-order'] = array(
            'label' => 'simpleOrder_NEW_STEP_ORDER',
            'note' => 'simpleOrder_NEW_STEP_ORDER_DESC',
            'params' => array(array('route' => 'app/step-order')),
        );
    }
} 