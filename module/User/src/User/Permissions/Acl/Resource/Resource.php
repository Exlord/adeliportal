<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 5/20/13
 * Time: 4:25 PM
 */

namespace User\Permissions\Acl\Resource;


/**
 * Class Resource
 * @package User\Permissions\Acl\Resource
 */
class Resource extends \Zend\Permissions\Acl\Resource\GenericResource
{
    /**
     * @var string The name of the resource
     * in case of route the name would be [route:name]
     */
    protected $name;
    protected $note;
    /**
     * @var string witch module this resource belongs to
     */
    protected $module;
    /**
     * @var string the FQN of the assert class
     */
    protected $assertClass = null;

    /**
     * @param null $assertClass
     */
    public function setAssertClass($assertClass)
    {
        $this->assertClass = $assertClass;
    }

    /**
     * @return null
     */
    public function getAssertClass()
    {
        return $this->assertClass;
    }


    /**
     * @param mixed $module
     */
    public function setModule($module)
    {
        $this->module = $module;
    }

    /**
     * @return mixed
     */
    public function getModule()
    {
        return $this->module;
    }


    /**
     * @param mixed $note
     */
    public function setNote($note)
    {
        $this->note = $note;
    }

    /**
     * @return mixed
     */
    public function getNote()
    {
        return $this->note;
    }


    /**
     * @param mixed $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    public function __construct($resourceId, $name, $module, $assertClass = null)
    {
        parent::__construct($resourceId);
        $this->name = $name;
        $this->module = $module;
        $this->assertClass = $assertClass;
    }
}