<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\View\Helper\Navigation;

use RecursiveIteratorIterator;
use User\Permissions\Acl\AclManager;
use Zend\Navigation\AbstractContainer;
use Zend\Navigation\Page\AbstractPage;
use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\View;
use Zend\View\Exception;

/**
 * Helper for rendering menus from navigation containers
 */
class Menu extends View\Helper\Navigation\Menu
{

    /**
     * Determines whether a page should be allowed given certain parameters
     *
     * @param   array $params
     * @return  bool
     */
    protected function isAllowed($params)
    {
        $accepted = true;
        $acl = $params['acl'];
        /* @var $page AbstractPage */
        $page = $params['page'];
        $role = $params['role'];

        $resource = $page->getResource();
        $accepted = AclManager::load()->IsAllowed(null, $resource);
        return $accepted;
    }

//    public function renderMenu($container = null, array $options = array())
//    {
//        return 'this is my menu';
//    }
}
