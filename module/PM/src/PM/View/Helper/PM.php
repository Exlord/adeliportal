<?php
namespace PM\View\Helper;


class PM extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke($userId)
    {
        return $this->view->render('pm/helper/pm', array('userId'=>$userId));
    }

}