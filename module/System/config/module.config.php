<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 11/22/12
 * Time: 9:21 AM
 */
namespace System;
return array(
    'controllers' => array(
        'invokables' => array(
            'System\Controller\Admin' => 'System\Controller\AdminController',
        ),
    ),
    'view_helpers' => array(
        'invokables' => array(
            'general' => 'System\View\Helper\General',
            'fieldset' => 'System\View\Helper\Fieldset',
            'buttonset' => 'System\View\Helper\Buttonset',
            'iptFormRow' => 'System\View\Helper\IPTFormRow',
            'iptFormElement' => 'System\View\Helper\IPTFormElement',
            'iptFormCollection' => 'System\View\Helper\IPTFormCollection',
            'iptFormMultiCheckbox' => 'System\View\Helper\IPTFormMultiCheckbox',
            'iptFormRadio' => 'System\View\Helper\IPTFormRadio',
            'iptFormText' => 'System\View\Helper\IPTFormText',
            'flashMessenger' => 'System\View\Helper\FlashMessenger',
            'iptFormCaptcha' => 'System\View\Helper\IPTFormCaptcha',
            'system_captcha_math' => 'System\Captcha\Helper\Math',
            'ipt_form_constant' => 'System\View\Helper\IPTFormConstant',
            'formText' => 'System\View\Helper\FormText',

            //Alert
            'alert' => 'TwbBundle\View\Helper\TwbBundleAlert',
            //Badge
            'badge' => 'TwbBundle\View\Helper\TwbBundleBadge',
            //Button group
            'buttonGroup' => 'TwbBundle\View\Helper\TwbBundleButtonGroup',
            //DropDown
            'dropDown' => 'TwbBundle\View\Helper\TwbBundleDropDown',
            //Form
            'form' => 'System\View\Helper\Form',
            'formButton' => 'TwbBundle\Form\View\Helper\TwbBundleFormButton',
            'formCheckbox' => 'TwbBundle\Form\View\Helper\TwbBundleFormCheckbox',
            'formCollection' => 'System\View\Helper\FormCollection',
            'formElement' => 'TwbBundle\Form\View\Helper\TwbBundleFormElement',
            'formElementErrors' => 'TwbBundle\Form\View\Helper\TwbBundleFormElementErrors',
            'formMultiCheckbox' => 'TwbBundle\Form\View\Helper\TwbBundleFormMultiCheckbox',
            'formRadio' => 'System\View\Helper\FormRadio',
            'formRow' => 'System\View\Helper\FormRow',
            'formStatic' => 'TwbBundle\Form\View\Helper\TwbBundleFormStatic',
            //Form Errors
            'formErrors' => 'TwbBundle\Form\View\Helper\TwbBundleFormErrors',
            //Glyphicon
            'glyphicon' => 'TwbBundle\View\Helper\TwbBundleGlyphicon',
            //FontAwesome
            'fontAwesome' => 'TwbBundle\View\Helper\TwbBundleFontAwesome',
            //Label
            'label' => 'TwbBundle\View\Helper\TwbBundleLabel',
        ),
        'factories' => array( //            'ModuleManager' => 'System\Mvc\Service\ModuleManagerFactory',
        )
    ),
    'translator' => array(
        'translation_file_patterns' => array(
            array(
                'type' => 'phpArray',
                'base_dir' => __DIR__ . '/../../../language',
                'pattern' => '%s/' . __NAMESPACE__ . '.lang',
            ),
        ),
    ),
    'router' => array(
        'routes' => array(
            'admin' => array(
                'child_routes' => array(
                    'configs' => array(
                        'child_routes' => array(
                            'captcha' => array(
                                'type' => 'Literal',
                                'options' => array(
                                    'route' => '/captcha',
                                    'defaults' => array(
                                        'controller' => 'System\Controller\Admin',
                                        'action' => 'captcha-config',
                                    ),
                                ),
                            ),
                        )
                    ),
                ),
            ),
        ),
    ),
    'navigation' => array(
        'admin_menu' => array(
            'configs' => array(
                'pages' => array(
                    array(
                        'label' => 'Captcha',
                        'route' => 'admin/configs/captcha',
                        'resource' => 'route:admin/configs/captcha',
                        'note' => '',
                    ),
                )
            )
        )
    ),
    'view_manager' => array(
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
        'template_map_path' => array(
            'empty' => __DIR__ . '/../view/empty.phtml'
        ),
    ),
);