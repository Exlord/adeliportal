<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/18/12
 * Time: 3:12 PM
 */

namespace User\View\Helper;

use User\Form;
use User\Model;
use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class LoginBlock extends \System\View\Helper\BaseHelper
{
    public function __invoke($block = null)
    {
        if ($block) {
            $block->blockId = 'user-login-block';
            $block->data['class'] .= 'user-login-block';
        }
        $user_identity = $this->getServiceManager()->get('user_identity');
        if ($user_identity->id == 0) {
            $user = new Model\User();
            $form = new Form\Login(urlencode(getSM('Request')->getRequestUri()));
            $form->bind($user);
            return $this->view->render('user/user/login', array('form' => $form));
        }
        return false;
    }
}
