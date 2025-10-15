<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Fields\Form\Element;

use Zend\Form\Element;
use Zend\Form\FormInterface;
use Zend\InputFilter\InputProviderInterface;

class Barcode extends UniqueCode
{
    public function getCode()
    {
        $val1 = explode('.', microtime(true));
        $val2 = mt_rand(1000000, 9999999);
        return $val1[0] . $val2;
    }

    public function __construct($name = null, $options = array())
    {
        parent::__construct($name, $options);
    }
}
