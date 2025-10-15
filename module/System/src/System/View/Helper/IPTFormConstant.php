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
use Zend\Form\Exception;

class IPTFormConstant extends Helper\AbstractHelper
{
    /**
     * Invoke helper as functor
     *
     * Proxies to {@link render()}.
     *
     * @param  ElementInterface|null $element
     * @return string|IPTFormConstant
     */
    public function __invoke(ElementInterface $element = null)
    {
        if (!$element) {
            return $this;
        }

        return $this->render($element);
    }

    /**
     * Render a form <input> element from the provided $element
     *
     * @param  ElementInterface $element
     * @throws Exception\DomainException
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $attributes = $element->getAttributes();
        $value = $element->getStaticValue();
        $elTag = $element->getOption('elementTag');
        if (!$elTag) $elTag = 'p';

        return sprintf(
            '<%s %s>%s</%s>',
            $elTag,
            $this->createAttributesString($attributes),
            $value,
            $elTag
        );
    }
}
