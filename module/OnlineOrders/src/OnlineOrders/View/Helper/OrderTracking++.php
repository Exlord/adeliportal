<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Koushan
 * Date: 6/19/13
 * Time: 5:42 PM
 * To change this template use File | Settings | File Templates.
 */

namespace OnlineOrders\View\Helper;

class OrderTracking extends \Zend\View\Helper\AbstractHelper
{
    public function __invoke()
    {
        $html = '';
        $form = new \OnlineOrders\Form\OrderTracking();
        $form->prepare();
        $html .= $this->view->form()->openTag($form);
        foreach($form->getElements() as $el){
            $html .= $this->view->iptFormRow($el);
        }
        $html .= $this->view->form()->closeTag();
        return $html;
    }

}