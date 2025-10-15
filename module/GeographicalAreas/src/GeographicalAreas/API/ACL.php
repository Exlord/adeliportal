<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:58 AM
 */
namespace GeographicalAreas\API;

use GeographicalAreas\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Geographical Areas Section',
                    'note' => 'Geographical Areas Section',
                    'route' => Module::GEOGRAPHICAL_AREAS,
                    'child_route' => array(
                        array(
                            'label' => 'Countries Section',
                            'note' => '',
                            'route' => Module::GEOGRAPHICAL_AREAS_COUNTRY,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::GEOGRAPHICAL_AREAS_COUNTRY_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::GEOGRAPHICAL_AREAS_COUNTRY_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::GEOGRAPHICAL_AREAS_COUNTRY_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::GEOGRAPHICAL_AREAS_COUNTRY_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'States Section',
                            'note' => 'States Section',
                            'route' => Module::GEOGRAPHICAL_AREAS_STATE,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::GEOGRAPHICAL_AREAS_STATE_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::GEOGRAPHICAL_AREAS_STATE_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::GEOGRAPHICAL_AREAS_STATE_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::GEOGRAPHICAL_AREAS_STATE_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Cities Section',
                            'note' => 'Cities Section',
                            'route' => Module::GEOGRAPHICAL_AREAS_CITY,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Cities Area Section',
                            'note' => '',
                            'route' => Module::GEOGRAPHICAL_AREAS_CITY_AREA,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_AREA_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_AREA_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_AREA_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::GEOGRAPHICAL_AREAS_CITY_AREA_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                    ),
                )
            ),
        );


        return $dataItemAcl;
    }
} 