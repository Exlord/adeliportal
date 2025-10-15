<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/27/2014
 * Time: 3:21 PM
 */

namespace OrgChart\API;


use Menu\Form\MenuItem;
use Zend\EventManager\Event;

class EventManager
{
    public function onLoadMenuTypes(Event $e)
    {
        /* @var $form MenuItem */
        $form = $e->getParam('form');

        $form->menuTypes['chart'] = array(
            'label' => 'OrgChart_CHART',
            'note' => '',
            'data-url' => url('admin/org-chart/chart-list'),
            'params' => array(
                array('route' => 'app/chart'),
                'chartId',
                'title',
            ),
            'template' => '[chartId] - [title]',
        );

    }
} 