<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 7/17/14
 * Time: 9:04 AM
 */

namespace System\View\Helper;


use TwbBundle\Form\View\Helper\TwbBundleFormRow;
use Zend\Validator\NotEmpty;

class FormRow extends TwbBundleFormRow
{
    public function render(\Zend\Form\ElementInterface $oElement)
    {
        if ($oElement->getOption('required') === true) {
//            $label = trim($oElement->getLabel());
//            if (!empty($label)) {
                $aLabelAttributes = $oElement->getLabelAttributes() ?: $this->labelAttributes;
                if (empty($aLabelAttributes['class'])) $aLabelAttributes['class'] = 'required';
                elseif (strpos($aLabelAttributes['class'], 'required') === false) $aLabelAttributes['class'] .= ' required';
                $oElement->setLabelAttributes($aLabelAttributes);
//            } else {
//                $class = $oElement->getAttribute('class');
//                if (!$class || empty($class)) $class = 'required';
//                elseif (strpos($class, 'required') === false) $class .= ' required';
//                $oElement->setAttribute('class', $class);
//            }
        }
        return parent::render($oElement);
    }

    /**
     * Render element's help block
     * @param \Zend\Form\ElementInterface $oElement
     * @return string
     */
    protected function renderHelpBlock(\Zend\Form\ElementInterface $oElement)
    {
        $sHelpBlock = false;
        if (!$sHelpBlock = $oElement->getOption('description'))
            $sHelpBlock = $oElement->getOption('help-block');
        return ($sHelpBlock) ? sprintf(
            self::$helpBlockFormat, ($oTranslator = $this->getTranslator()) ? $oTranslator->translate($sHelpBlock, $this->getTranslatorTextDomain()) : $sHelpBlock
        ) : '';
    }
} 