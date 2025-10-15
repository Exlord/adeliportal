<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/30/2014
 * Time: 11:57 AM
 */

namespace Gallery\API;


use Gallery\Module;

class ACL
{
    public static function load()
    {
        $dataItemAcl[] = array(
            'route' => 'route:admin',
            'child_route' => array(
                array(
                    'label' => 'Banner Section',
                    'note' => 'Banner Section',
                    'route' => Module::ADMIN_BANNER,
                    'child_route' => array(
                        array(
                            'label' => 'View Widget Info',
                            'note' => '',
                            'route' => Module::ADMIN_BANNER_WIDGET,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Extension',
                            'note' => '',
                            'route' => Module::ADMIN_BANNER_EXTENSION,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Configs',
                            'note' => '',
                            'route' => Module::ADMIN_BANNER_CONFIGS,
                            'child_route' => ''
                        ),
                        array(
                            'label' => 'Orders Banner List',
                            'note' => '',
                            'route' => Module::ADMIN_BANNER_LIST,
                            'child_route' => array(
                                array(
                                    'label' => 'View All',
                                    'note' => 'View All Even if by Module not registered',
                                    'route' => Module::ADMIN_BANNER_LIST_ALL,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => '',
                                    'route' => Module::ADMIN_BANNER_LIST_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_BANNER_LIST_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete Images',
                                    'note' => '',
                                    'route' => Module::ADMIN_BANNER_LIST_DELETE_IMAGE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_BANNER_LIST_UPDATE,
                                    'child_route' => ''
                                ),
                            )
                        ),
                        array(
                            'label' => 'Banner Groups Section',
                            'note' => 'Banner Groups Section',
                            'route' => Module::ADMIN_BANNER_GROUPS,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_BANNER_GROUPS_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_BANNER_GROUPS_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_BANNER_GROUPS_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_BANNER_GROUPS_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Banner Items Section',
                            'note' => 'Banner Items Section',
                            'route' => Module::ADMIN_BANNER_ITEM,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_BANNER_ITEM_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_BANNER_ITEM_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_BANNER_ITEM_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_BANNER_ITEM_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),

                    ),
                ),
                array(
                    'label' => 'Gallery Section',
                    'note' => 'Gallery Section',
                    'route' => Module::ADMIN_GALLERY,
                    'child_route' => array(
                        array(
                            'label' => 'Gallery Groups Section',
                            'note' => 'Gallery Groups Section',
                            'route' => Module::ADMIN_GALLERY_GROUPS,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_GALLERY_GROUPS_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_GALLERY_GROUPS_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_GALLERY_GROUPS_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_GALLERY_GROUPS_UPDATE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Config',
                                    'note' => '',
                                    'route' => 'route:admin/gallery/configs',
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Gallery Items Section',
                            'note' => 'Gallery Items Section',
                            'route' => Module::ADMIN_GALLERY_ITEM,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_GALLERY_ITEM_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_GALLERY_ITEM_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_GALLERY_ITEM_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_GALLERY_ITEM_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),

                    ),
                ),
                array(
                    'label' => 'Sliders Section',
                    'note' => 'Sliders Section',
                    'route' => Module::ADMIN_SLIDER,
                    'child_route' => array(
                        array(
                            'label' => 'Slider Groups Section',
                            'note' => 'Slider Groups Section',
                            'route' => Module::ADMIN_SLIDER_GROUPS,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_SLIDER_GROUPS_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_SLIDER_GROUPS_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_SLIDER_GROUPS_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_SLIDER_GROUPS_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Slider Items Section',
                            'note' => 'Slider Items Section',
                            'route' => Module::ADMIN_SLIDER_ITEM,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_SLIDER_ITEM_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_SLIDER_ITEM_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_SLIDER_ITEM_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_SLIDER_ITEM_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),

                    ),
                ),
                array(
                    'label' => 'Images Box Section',
                    'note' => 'Images Box Section',
                    'route' => Module::ADMIN_IMAGE_BOX,
                    'child_route' => array(
                        array(
                            'label' => 'Images Box Groups Section',
                            'note' => 'Images Box Groups Section',
                            'route' => Module::ADMIN_IMAGE_BOX_GROUPS,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_IMAGE_BOX_GROUPS_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_IMAGE_BOX_GROUPS_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_IMAGE_BOX_GROUPS_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_IMAGE_BOX_GROUPS_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),
                        array(
                            'label' => 'Images Box Items Section',
                            'note' => 'Images Box Items Section',
                            'route' => Module::ADMIN_IMAGE_BOX_ITEM,
                            'child_route' => array(
                                array(
                                    'label' => 'Create',
                                    'note' => 'Create',
                                    'route' => Module::ADMIN_IMAGE_BOX_ITEM_NEW,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Edit',
                                    'note' => 'Edit',
                                    'route' => Module::ADMIN_IMAGE_BOX_ITEM_EDIT,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Delete',
                                    'note' => 'Delete',
                                    'route' => Module::ADMIN_IMAGE_BOX_ITEM_DELETE,
                                    'child_route' => ''
                                ),
                                array(
                                    'label' => 'Update',
                                    'note' => 'Update',
                                    'route' => Module::ADMIN_IMAGE_BOX_ITEM_UPDATE,
                                    'child_route' => ''
                                ),
                            ),
                        ),

                    ),
                ),
            ),
        );

        return $dataItemAcl;
    }
} 