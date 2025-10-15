<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 9/30/2014
 * Time: 11:47 AM
 */

namespace System\Form\Element;


class Collection extends \Zend\Form\Element\Collection
{
    public function setTargetElement($elementOrFieldset)
    {
        $result = parent::setTargetElement($elementOrFieldset);
        $this->targetElement->initialize($this);
        return $result;
    }

} 