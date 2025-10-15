<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 10/6/2014
 * Time: 10:30 AM
 */

namespace System\View\Helper;


use TwbBundle\Form\View\Helper\TwbBundleFormCollection;

class FormCollection extends TwbBundleFormCollection
{
    /**
     * @var string
     */
    protected static $helpBlockFormat = '<p class="help-block">%s</p>';

    /**
     * @var string
     */
    protected $fieldsetFormat = '<fieldset%s>__HELP_BLOCK__%s</fieldset>';

    public function render(\Zend\Form\ElementInterface $oElement)
    {
        $label = trim($oElement->getLabel());
        if (empty($label))
            $oElement->setAttribute('class', $oElement->getAttribute('class') . ' border-less');
        $output = parent::render($oElement);
        $helpBlock = $this->renderHelpBlock($oElement);
        $output = str_replace('__HELP_BLOCK__', $helpBlock, $output);

        if ($oElement instanceof \Zend\Form\FieldsetInterface) {
            $columnClass = $oElement->getOption('column_class');
            if ($columnClass)
                $output = "<div class='{$columnClass}'>{$output}</div>";
        }

        return $output;
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