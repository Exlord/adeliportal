<?php

namespace Assetic;

use System\Module\AbstractModule;
use Zend\Loader\StandardAutoloader;
use Zend\Loader\AutoloaderFactory;

/**
 * Module class
 *
 * @category   AssetManager
 * @package    AssetManager
 */
class Module extends AbstractModule
{
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
    protected $autoLoadClassMap = false;
}
