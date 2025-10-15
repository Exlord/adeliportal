<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Email adeli.farhad@gmail.com
 * Date: 6/1/13
 * Time: 12:58 PM
 */

namespace User\Permissions\Acl;

use Application\API\App;
use User\Permissions\Acl\Resource\Resource;

/**
 * Class AclManager
 * @package User\Permissions\Acl
 */
class AclManager
{
    /**
     * @var Acl
     */
    public static $instance;

    /**
     * @return Acl
     */
    public static function load($permissions = null)
    {
        if (self::$instance == null) {
            self::$instance = getCache(true)->getItem(Acl::ACL_CACHE_ID);
            if (!self::$instance) {
                self::$instance = new Acl();
                self::$instance->init($permissions);
            }
        }
        return self::$instance;
    }

    public static function reload($permissions = null)
    {
        getCache(true)->removeItem(Acl::ACL_CACHE_ID);
        self::$instance = null;
        return self::load($permissions);
    }

    /**
     * @param $data
     * @param Acl $acl
     * @param $namespace
     * @param null $parent
     */
    public static function LoadFromArray($data, $acl, $namespace, $parent = null)
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
                    self::LoadFromArray($val['child_route'], $acl, $namespace, $val['route']);
                }
            }
        }
    }
}