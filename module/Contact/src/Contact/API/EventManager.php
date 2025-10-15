<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 12:49 PM
 */

namespace Contact\API;


use Menu\Form\MenuItem;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['contact'] = array(
            'label' => 'Contact Us',
            'note' => 'Show All Members from Section Contact Us',
            'params' => array(array('route' => 'app/contact')),
        );

        $form->menuTypes['single-contact'] = array(
            'label' => 'Contact Us',
            'note' => 'Show Single Member from Section Contact Us',
            'data-url' => url('admin/contact/user/menu-contact-user-list'),
            'params' => array(
                array('route' => 'app/contact/single'),
                'contactId',
            ),
            'template' => '[title]',
        );

        $form->menuTypes['custom-contact'] = array(
            'label' => 'Contact Us',
            'note' => 'Show members by category',
            'data-url' => url('admin/contact/user/menu-contact-category-list'),
            'params' => array(
                array('route' => 'app/contact/category'),
                'catId',
            ),
            'template' => '[catId] - [title]',
        );

        $form->menuTypes['representative'] = array(
            'label' => 'Representative',
            'note' => '',
            'params' => array(array('route' => 'app/representative')),
        );
    }
} 