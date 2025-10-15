<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/18/12
 * Time: 1:52 PM
 */
namespace System\View\Helper;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class BaseHelper extends AbstractHelper implements ServiceLocatorAwareInterface
{
    protected $HelperPluginManager;
    protected $paramsPlugin = null;
    protected $loaded = false;

    /**
     * @return \Zend\Mvc\Controller\Plugin\Params
     */
    protected function params()
    {
        if (is_null($this->paramsPlugin))
            $this->paramsPlugin = getSM()->get('ControllerPluginManager')->get('params');
        return $this->paramsPlugin;
    }

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     * @return AbstractHelper
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->HelperPluginManager = $serviceLocator;
        return $this;
    }

    /**
     * Get view helper service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->HelperPluginManager;
    }

    /**
     * @return ServiceLocatorInterface
     */
    public function getServiceManager()
    {
        return $this->getServiceLocator()->getServiceLocator();
    }

    /**
     * @return bool
     */
    public function isLoaded()
    {
        return $this->loaded;
    }
}
