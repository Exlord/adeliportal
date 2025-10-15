<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 2/19/14
 * Time: 10:50 AM
 */

namespace System\Form\Element;

use Zend\Form\Element;
use Zend\InputFilter\InputProviderInterface;

class Constant extends Element
{
    protected $staticValue = '';

    /**
     * @param string $staticValue
     */
    public function setStaticValue($staticValue)
    {
        $this->staticValue = $staticValue;
    }

    /**
     * @return string
     */
    public function getStaticValue()
    {
        return $this->staticValue;
    }

}