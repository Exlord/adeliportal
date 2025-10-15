<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace System\View\Helper;

use Zend\Form\ElementInterface;
use Zend\Form\View\Helper;

class FormText extends Helper\FormText
{
    public function render(ElementInterface $element)
    {
        $html = parent::render($element);
        if ($element->getOption('postFix'))
            $html .= "<span class='element_postfix'>" . $element->getOption('postFix') . "</span>";

        return $html;
    }
}
