<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace System\View\Helper;

use Fields\Form\Element\Barcode;
use Fields\Form\Element\UniqueCode;
use Zend\Form\Element\Captcha;
use Zend\Form\ElementInterface;
use Zend\Form\Exception;
use Zend\Form\View\Helper\AbstractHelper;
use Zend\Form\View\Helper;

class IPTFormRow extends Helper\FormRow
{

    /**
     * Utility form helper that renders a label (if it exists), an element and errors
     *
     * @param ElementInterface $element
     * @return string
     * @throws \Zend\Form\Exception\DomainException
     */
    public function render(ElementInterface $element)
    {
        $escapeHtmlHelper = $this->getEscapeHtmlHelper();
        $labelHelper = $this->getLabelHelper();
        $elementHelper = $this->getElementHelper($element);
        $elementErrorsHelper = $this->getElementErrorsHelper();
        $elementErrorsHelper->setAttributes(array('class' => 'form-element-error'));
        if (!$element->hasAttribute('id')) {
            $id = str_replace('[', '_', str_replace(']', '_', $element->getName()));
            if ($id[strlen($id) - 1] == '_')
                $id = substr($id, 0, strlen($id) - 1);
            $element->setAttribute('id', $id);
        }

        $label = $element->getLabel();
        $inputErrorClass = $this->getInputErrorClass();
        $elementErrors = $elementErrorsHelper->render($element);

        // Does this element have errors ?
        if (!empty($elementErrors) && !empty($inputErrorClass)) {
            $classAttributes = ($element->hasAttribute('class') ? $element->getAttribute('class') . ' ' : '');
            $classAttributes = $classAttributes . $inputErrorClass;

            $element->setAttribute('class', $classAttributes);
        }

        $elementString = $elementHelper->render($element);

        $sElementType = $element->getAttribute('type');
        if ($sElementType === 'submit' || $sElementType === 'button' || $sElementType === 'reset')
            return $elementString;

        $description = $element->getOption('description');
        $description_params = null;
        if (is_array($description)) {
            $description_params = $description;
            $description = array_shift($description_params);
        }
        if ($description) {
            if (null !== ($translator = $this->getTranslator())) {
                $description = $translator->translate(
                    $description, $this->getTranslatorTextDomain()
                );
            }
            if ($description_params)
                $description = vsprintf($description, $description_params);
            $description = sprintf("<p class='form_element_description'>%s</p>", $description);
        } else
            $description = '';

        $required = $element->getOption('required');
        if ($required && $required == true && !$element instanceof Captcha)
            $required = sprintf("<span  rel='tooltip' title='%s' class='required'></span>", t(\Zend\Validator\NotEmpty::IS_EMPTY));
        else
            $required = '';

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
            $viewType = $element->getOption('view-type');
            $type = $element->getAttribute('type');
            if ($type === 'multi_checkbox' || $type === 'radio' && $viewType != 'inline') {
                $class = 'items';
                $markup = sprintf(
                    "<fieldset><legend>%s</legend>%s<div class='{$class}'>%s</div></fieldset>",
                    $label,
                    $description,
                    $elementString);
            } else {
                if ($element->hasAttribute('id')) {
                    $labelOpen = '';
                    $labelClose = '';
                    $label = $labelHelper($element);
                } else {
                    $labelOpen = $labelHelper->openTag($labelAttributes);
                    $labelClose = $labelHelper->closeTag();
                }

                if ($label !== '' && !$element->hasAttribute('id')) {
                    $label = '<span>' . $label . '</span>';
                }

                if ($type == 'checkbox')
                    $markup = $elementString;
                else {
                    switch ($this->labelPosition) {
                        case self::LABEL_PREPEND:
                            $markup = $labelOpen . $label . $labelClose . $required . $elementString;
                            break;
                        case self::LABEL_APPEND:
                        default:
                            $markup = $elementString . $required . $labelOpen . $label . $labelClose;
                            break;
                    }
                }
                $markup .= $description;
            }


            if ($this->renderErrors) {
                $markup .= $elementErrors;
            }

            $markup = sprintf("<div class='form_element " . $type . "'>%s</div>", $markup);
        } else {
            if ($this->renderErrors) {
                $markup = $required . $elementString . $description . $elementErrors;
            } else {
                $markup = $required . $elementString . $description;
            }
        }

        return $markup;
    }

    protected function getElementHelper(ElementInterface $element)
    {
        if (method_exists($this->view, 'plugin')) {
            $type = $element->getAttribute('type');
            if ('multi_checkbox' == $type) {
                return $this->view->plugin('ipt_form_multi_checkbox');
            }

            if ('radio' == $type) {
                return $this->view->plugin('ipt_form_radio');
            }

            if ('text' == $type && !($element instanceof Barcode)) {
                return $this->view->plugin('ipt_form_text');
            }

            if ($element instanceof \Zend\Form\Element\Captcha) {
                return $this->view->plugin('ipt_form_captcha');
            }
        }

        if ($this->elementHelper) {
            return $this->elementHelper;
        }

        if (method_exists($this->view, 'plugin')) {
            $this->elementHelper = $this->view->plugin('iptFormElement');
        }

        if (!$this->elementHelper instanceof IPTFormElement) {
            $this->elementHelper = new IPTFormElement();
        }

        return $this->elementHelper;
    }
}
