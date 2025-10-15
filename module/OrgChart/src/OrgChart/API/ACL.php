<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 11/1/2014
 * Time: 10:03 AM
 */

namespace OrgChart\API;


use OrgChart\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'OrgChart_CHART',
            'note' => '',
            'route' => Module::APP_CHART,
            'child_route' => '',
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'OrgChart_CHART',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_ORG_CHART,
                    'child_route' => array(
                        array(
                            'label' => 'Configs',
                            'note' => '',
                            'route' => Module::ADMIN_ORG_CHART_CONFIG,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'OrgChart_CHARTLIST',
                            'note' => 'OrgChart_CHART_LIST_MENU',
                            'route' => Module::ADMIN_ORG_CHART_CHART_LIST,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Create',
                            'note' => '',
                            'route' => Module::ADMIN_ORG_CHART_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => '',
                            'route' => Module::ADMIN_ORG_CHART_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => '',
                            'route' => Module::ADMIN_ORG_CHART_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => '',
                            'route' => Module::ADMIN_ORG_CHART_UPDATE,
                            'child_route' => ''
                        ),
                    ),
                ),
                array(
                    'label' => 'OrgChart_NODECHART',
                    'note' => 'In Management Section',
                    'route' => Module::ADMIN_CHART_NODE,
                    'child_route' => array(
                        array(
                            'label' => 'Parent Node',
                            'note' => '',
                            'route' => Module::ADMIN_CHART_NODE_PARENT_NODE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Create',
                            'note' => '',
                            'route' => Module::ADMIN_CHART_NODE_NEW,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit',
                            'note' => '',
                            'route' => Module::ADMIN_CHART_NODE_EDIT,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete',
                            'note' => '',
                            'route' => Module::ADMIN_CHART_NODE_DELETE,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update',
                            'note' => '',
                            'route' => Module::ADMIN_CHART_NODE_UPDATE,
                            'child_route' => ''
                        ),
                    ),
                )
            ),
        );


        return $dataItemAcl;
    }
} 