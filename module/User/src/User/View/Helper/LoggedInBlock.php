<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/19/12
 * Time: 10:09 AM
 */
namespace User\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoggedInBlock extends \System\View\Helper\BaseHelper
{
    public function __invoke($layout = 'loggedInBlock')
    {
        $route = getSM()->get('Request')->getRequestUri();
        $path = strpos($route, 'admin') > -1 ? url('admin/users/view', array('id' => current_user()->id)) : url('app/user');
        if (current_user()->username != 'Guest') {
            return $this->view->render('user/user/' . $layout, array('path' => $path));
        }
        return false;
    }
}
