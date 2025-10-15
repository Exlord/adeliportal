<?php

$file = __DIR__ . "/clients/" . ACTIVE_SITE . '.config.php';
if (file_exists($file))
    $array = include $file;

$main_array = array(
    'module_listener_options' => array(
        'config_glob_paths' => array(
            'config/autoload/{,*.}{global,local}.php',
        ),
        'module_paths' => array(
            './module',
            './vendor',
        ),
        'check_dependencies' => false,
    ),
    'cache' => array(
        'adapter' => array(
            'name' => 'Filesystem',
            'options' => array(
                'cache_dir' => ROOT . '/data/' . ACTIVE_SITE . '/cache',
            ),
        ),
        'options' => array(
            'ttl' => 604800
        ),
        'plugins' => array(
            'exception_handler' => array('throw_exceptions' => true),
            'Serializer'
        ),
    ),
    'session' => array(
        'remember_me_seconds' => 2419200,
        'cookie_life_time' => 2419200,
        'use_cookies' => true,
        'cookie_httponly' => true,
    ),
);

if (!IS_DEVELOPMENT_SERVER) {
    $main_array['module_listener_options']['config_cache_enabled'] = true;
    $main_array['module_listener_options']['config_cache_key'] = 'application_merged_configs';
    $main_array['module_listener_options']['module_map_cache_enabled'] = true;
    $main_array['module_listener_options']['module_map_cache_key'] = 'modules_class_map';
    $main_array['module_listener_options']['cache_dir'] = ROOT . '/data/' . ACTIVE_SITE . '/cache';
}
if (is_array($array) && count($array))
    $main_array = array_merge_recursive($array, $main_array);

return $main_array;
