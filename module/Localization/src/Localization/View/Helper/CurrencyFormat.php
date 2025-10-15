<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Localization\View\Helper;

use Locale;
use NumberFormatter;
use Zend\I18n\Exception;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper for formatting currency.
 */
class CurrencyFormat extends \Zend\I18n\View\Helper\CurrencyFormat
{
    public function __invoke(
        $number,
        $currencyCode = null,
        $showDecimals = null,
        $locale = null,
        $pattern = null
    )
    {
        if (!$currencyCode) {
            $currencyCode = getCurrency();
        }

        $oldCurrencyCode = $currencyCode;
        if ($currencyCode == 'IRT')
            $currencyCode = 'IRR';
        // call parent and get the string
        $string = parent::__invoke($number, $currencyCode, $showDecimals, $locale, $pattern);
        $currencyCode = $this->getCurrencyCode();
        if ($currencyCode == 'IRR') {
            $showDecimals = false;
        }

        if ($oldCurrencyCode)
            $currencyCode = $oldCurrencyCode;
        if ($currencyCode == 'IRR') {
            $string = str_replace('٬', ',', $string);
            $string = str_replace('﷼', '<span class="currency-code">ریال</span>', $string);
        } elseif ($currencyCode == 'IRT') {
            $string = str_replace('٬', ',', $string);
            $string = str_replace('﷼', '<span class="currency-code">تومان</span>', $string);
        }
        return $string;
    }
}
