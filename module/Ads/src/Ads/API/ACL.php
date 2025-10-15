<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:37 AM
 */

namespace Ads\API;


use Ads\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'label' => 'ADS_AD',
            'note' => '',
            'route' => 'route:app/ad',
            'child_route' => array(
                array(
                    'label' => 'View All Info',
                    'note' => '',
                    'route' => Module::APP_AD_VIEW_ALL_INFO,
                    'child_route' => ''
                ),
            ),
        );

        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'ADS_AD',
                    'note' => 'In Management Section',
                    'route' => 'route:admin/ad',
                    'child_route' => array(
                        array(
                            'label' => 'View List',
                            'note' => '',
                            'route' => 'route:admin/ad/list',
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => '',
                                    'route' => Module::ADMIN_AD_LIST_ALL,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'ADS_CHANGE_ALL_FIELD',
                                    'note' => '',
                                    'route' => Module::ADMIN_AD_LIST_CHANGE_ALL_FIELD,
                                    'child_route' => ''
                                ),
                            )
                        ),
                        array(
                            'label' => 'Create',
                            'note' => 'Create',
                            'route' => 'route:admin/ad/new',
                            'child_route' => array(
                                array(
                                    'label' => 'ADS_NEW_PAYMENT',
                                    'note' => '',
                                    'route' => Module::ADMIN_AD_NEW_PAYMENT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'ADS_NEW_APPROVED',
                                    'note' => '',
                                    'route' => Module::ADMIN_AD_NEW_APPROVED,
                                    'child_route' => ''
                                ),
                            )
                        ),
                        array(
                            'label' => 'ADS_ALLOWED_REF',
                            'note' => '',
                            'route' => Module::ADMIN_AD_NEW_REF,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Edit Section',
                            'note' => '',
                            'route' => 'route:admin/ad/edit',
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Delete Section',
                            'note' => '',
                            'route' => 'route:admin/as/delete',
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Update Section',
                            'note' => '',
                            'route' => 'route:admin/ad/update',
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Configs',
                            'note' => '',
                            'route' => 'route:admin/ad/config',
                            'child_route' => array(
                                array(
                                    'label' => 'New Base Type',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/new-type',
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'First Level',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/first-config',
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Second Level',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/second-config',
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Third Level',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/third-config',
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Four Level',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/four-config',
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Advance Level',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/advance-config',
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Select Fields',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/select-fields',
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Filters Fields',
                                    'note' => '',
                                    'route' => 'route:admin/ad/config/filter-fields',
                                    'child_route' => ''
                                ),
                            )
                        ),
                    ),
                )
            ),
        );
        
        return $dataItemAcl;
    }

} 