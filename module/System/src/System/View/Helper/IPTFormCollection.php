<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace System\View\Helper;

use RuntimeException;
use Zend\Form\Element;
use Zend\Form\ElementInterface;
use Zend\Form\Element\Collection as CollectionElement;
use Zend\Form\FieldsetInterface;
use Zend\View\Helper\AbstractHelper as BaseAbstractHelper;

class IPTFormCollection extends \Zend\Form\View\Helper\FormCollection
{
    /**
     * The name of the default view helper that is used to render sub elements.
     *
     * @var string
     */
    protected $defaultElementHelper = 'iptFormRow';


    /**
     * Render a collection by iterating through all fieldsets and elements
     *
     * @param  ElementInterface $element
     * @return string
     */
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $markup = '';
        $templateMarkup = '';
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $elementHelper = $this->getElementHelper();
        $fieldsetHelper = $this->getFieldsetHelper();

        $attributes = $element->getAttributes();
        $attributes = $this->createAttributesString($attributes);

        $description = $element->getOption('description');
        if ($description) {
            if (null !== ($translator = $this->getTranslator())) {
                $description = $translator->translate(
                    $description, $this->getTranslatorTextDomain()
                );
            }
            $description = sprintf("<p class='form_element_description'>%s</p>", $description);
        } else
            $description = '';

        if ($element instanceof CollectionElement && $element->shouldCreateTemplate()) {
            $templateMarkup = $this->renderTemplate($element);
        }

        foreach ($element->getIterator() as $elementOrFieldset) {
            if ($elementOrFieldset instanceof FieldsetInterface) {
                $markup .= $fieldsetHelper($elementOrFieldset);
            } elseif ($elementOrFieldset instanceof ElementInterface) {
                $markup .= $elementHelper($elementOrFieldset);
            }
        }

        // If $templateMarkup is not empty, use it for simplify adding new element in JavaScript
        if (!empty($templateMarkup)) {
            $markup .= $templateMarkup;
        }

        // Every collection is wrapped by a fieldset if needed
        if ($this->shouldWrap) {
            $label = $element->getLabel();

            if (!empty($label)) {

                if (null !== ($translator = $this->getTranslator())) {
                    $label = $translator->translate(
                        $label, $this->getTranslatorTextDomain()
                    );
                }

                $label = $escapeHtmlHelper($label);
                $labelAttributes = $this->createAttributesString($element->getLabelAttributes() ? : array());

                $markup = sprintf(
                    '<fieldset %s><legend %s>%s</legend>%s%s</fieldset>',
                    $attributes,
                    $labelAttributes,
                    $label,
                    $description,
                    $markup
                );
            } else {
                $markup = $description . $markup;
            }
        }

        return $markup;
    }

}
