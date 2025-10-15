<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Exlord
 * Date: 12/22/12
 * Time: 10:43 AM
 */
namespace System\View\Helper;

use Zend\View\Helper\AbstractHelper;
use Zend\ServiceManager\ServiceLocatorInterface;

class Buttonset extends \Zend\Form\View\Helper\FormRow
{
    protected function getElementRendered($element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }

        $type = $element->getAttribute('type');

        if ('multi_checkbox' == $type) {
            $helper = $renderer->plugin('system_multi_checkbox');
            return $helper($element);
        }

        if ('radio' == $type) {
            $helper = $renderer->plugin('system_radio');
            return $helper($element);
        }

        $helper = $renderer->plugin('form_input');
        return $helper($element);
    }

    public function render($element)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementString = $this->getElementRendered($element);
        $elementErrorsHelper = $this->getElementErrorsHelper();

        $label = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();
        $elementErrors = $elementErrorsHelper->render($element);

        // Does this element have errors ?
        if (!empty($elementErrors) && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        if (isset($label) && '' !== $label) {
            // Translate the label
            if (null !== ($translator = $this->getTranslator())) {
                $label = $translator->translate(
                    $label, $this->getTranslatorTextDomain()
                );
            }

            $label = $escapeHtmlHelper($label);
            $labelAttributes = $element->getLabelAttributes();

            if (empty($labelAttributes)) {
                $labelAttributes = $this->labelAttributes;
            }

            // Multicheckbox elements have to be handled differently as the HTML standard does not allow nested
            // labels. The semantic way is to group them inside a fieldset
            $type = $element->getAttribute('type');
            $markup = sprintf(
                '<fieldset><legend>%s</legend><div class="buttonset">%s</div></fieldset>',
                $label,
                $elementString);

            if ($this->renderErrors) {
                $markup .= $elementErrors;
            }
        } else {
            if ($this->renderErrors) {
                $markup = $elementString . $elementErrors;
            } else {
                $markup = $elementString;
            }
        }

        return $markup;
    }
}
