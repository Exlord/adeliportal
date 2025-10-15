<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 4/13/13
 * Time: 3:22 PM
 */

namespace System\API;


use Zend\EventManager\EventManager;
use Zend\EventManager\EventManagerAwareInterface;
use Zend\EventManager\EventManagerInterface;
use Zend\EventManager\SharedEventManager;
use Zend\EventManager\SharedEventManagerAwareInterface;
use Zend\EventManager\SharedEventManagerInterface;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;

abstract class BaseAPI implements ServiceLocatorAwareInterface,
    EventManagerAwareInterface
{

    /**
     * @param $params
     * @return \Zend\Navigation\Page\AbstractPage
     */
    public static function makeMenuUrl($params)
    {
        return null;
    }

    /**
     * @param $params
     * @return string
     */
    public static function getMenuUrl($params)
    {
        $page = static::makeMenuUrl($params);
        $link = '#';
        try {
            $link = $page->getHref();
        } catch (\Exception $e) {
            db_log_exception($e);
        }
        return $link;
    }

    /**
     * @var ServiceLocatorInterface
     */
    protected $services;

    /**
     * Set service locator
     *
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->services = $serviceLocator;
    }

    /**
     * Get service locator
     *
     * @return ServiceLocatorInterface
     */
    public function getServiceLocator()
    {
        return $this->services;
    }

    /**
     * @var EventManagerInterface
     */
    protected $_eventManager;
    /**
     * @var SharedEventManagerInterface
     */
    protected $_sharedEventManager;

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

    protected function render($nameOrModel, $values = null)
    {
        return getSM('viewrenderer')->render($nameOrModel, $values);
    }

    /**
     * @return \Fields\API\Fields
     */
    protected static function getFieldsApi()
    {
        return getSM()->get('fields_api');
    }

    protected static function hasFieldsApi()
    {
        return getSM()->has('fields_api');
    }

    protected static function getVHM()
    {
        return getSM('ViewHelperManager');
    }
}