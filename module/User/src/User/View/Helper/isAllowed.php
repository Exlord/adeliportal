<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 3/3/13
 * Time: 12:29 PM
 */
namespace User\View\Helper;

class isAllowed extends \System\View\Helper\BaseHelper
{
    public function __construct()
    {
    }

    public function __invoke($resource,$privilege)
    {
        $user_identity = $this->getServiceManager()->get('user_identity');
        if ($user_identity->id == 0) {
            $user = new \User\Model\User();

            $form = new \User\Form\Login();
            $form->setInputFilter(new \User\Form\InputFilter\Login())
                ->bind($user);
            return array('block_title' => 'Login', 'block_content' => $this->view->render('user/user/login', array('form' => $form)));
        }
        return false;
    }
}
