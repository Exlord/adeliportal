<?php
/**
 * Created by PhpStorm.
 * User: Exlord
 * Date: 11/16/13
 * Time: 10:55 AM
 */

namespace System\View\Helper;


use Fields\Form\Element\Barcode;
use System\Form\Element\Constant;
use Zend\Form\ElementInterface;
use Zend\Form\View\Helper\FormElement;

class IPTFormElement extends FormElement
{
    public function render(ElementInterface $element)
    {
        $renderer = $this->getView();
        if (!method_exists($renderer, 'plugin')) {
            // Bail early if renderer is not pluggable
            return '';
        }
        if ($element instanceof Barcode) {
            $helper = $renderer->plugin('field_barcode');
            return $helper($element);
        }

        if ($element instanceof Constant) {
            $helper = $renderer->plugin('ipt_form_constant');
            return $helper($element);
        }

        return parent::render($element);
    }
} 