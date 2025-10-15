<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Menu\Navigation\Service;

/**
 * Constructed factory to set pages during construction.
 */
class DynamicNavigationFactory extends \Zend\Navigation\Service\ConstructedNavigationFactory
{

    protected $name;

    public function __construct($name, $config)
    {
        $this->config = $config;
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }
}
