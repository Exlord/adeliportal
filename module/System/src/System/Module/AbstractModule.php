<?php

namespace System\Module;

use User\API;
use User\Permissions\Acl\Acl;
use User\Permissions\Acl\Resource\Resource;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use Zend\ModuleManager\ModuleManager;
use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

abstract class AbstractModule implements
    AutoloaderProviderInterface,
    LocatorRegisteredInterface,
    EventManagerAwareInterface
{
    /**
     * @var $sm \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $sm;
    protected $dir = __DIR__;
    protected $namespace = __NAMESPACE__;
    protected $autoLoadClassMap = true;
    /**
     * @var EventManagerInterface
     */
    protected $_eventManager;
    /**
     * @var SharedEventManagerInterface
     */
    protected $_sharedEventManager;

    public function getDir()
    {
        return $this->dir;
    }

    public function getNamespace()
    {
        return $this->namespace;
    }

    public function init(ModuleManager $moduleManager)
    {
        $em = $moduleManager->getEventManager();
        $this->setEventManager($em);
    }

    public function onBootstrap(MvcEvent $e)
    {
        $this->sm = $e->getApplication()->getServiceManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($this->getEventManager());
    }

    /**
     * @param \Zend\EventManager\EventManagerInterface $em
     * @return void|\Zend\EventManager\EventManagerInterface
     */
    public function setEventManager(EventManagerInterface $em)
    {
        $em->setIdentifiers(array(
            __CLASS__,
            get_called_class(),
        ));
        $this->_eventManager = $em;
        return $this->_eventManager;
    }

    /**
     * @return \Zend\EventManager\EventManagerInterface
     */
    public function getEventManager()
    {
        if (null === $this->_eventManager) {
            $this->setEventManager(new EventManager());
        }
        return $this->_eventManager;
    }

    public function getAutoloaderConfig()
    {
        $load = array();
        if ($this->autoLoadClassMap) {
            $load['Zend\Loader\ClassMapAutoloader'] = array(
                $this->getDir() . '/autoload_classmap.php',
            );
        }
        $load['Zend\Loader\StandardAutoloader'] = array(
            'namespaces' => array(
                $this->getNamespace() => $this->getDir() . '/src/' . $this->getNamespace(),
            )
        );

        return $load;
    }

    public function getConfig()
    {
        return include $this->getDir() . '/config/module.config.php';
    }

    /**
     * @param \User\Permissions\Acl\Acl $acl
     * @return \User\Permissions\Acl\Acl
     */
    public function getAcl($acl)
    {
        return array();
    }

    /**
     * @param $data
     * @param $acl Acl
     * @param $namespace
     */
    protected function makeAcl($data, $acl, $namespace, $parent = null)
    {
        if (is_array($data) && count($data)) {
            foreach ($data as $val) {
                if (isset($val['label']) && $val['route']) {
                    $resource = new Resource($val['route'], $val['label'], $namespace);
                    if (isset($val['note']))
                        $resource->setNote($val['note']);

                    $acl->addResource($resource, $parent);
                }
                if (isset($val['child_route']) && isset($val['route'])) {
                    $this->makeAcl($val['child_route'], $acl, $namespace, $val['route']);
                }
            }
        }
    }
}
