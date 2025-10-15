<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/zf2 for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Localization\View\Helper;

use DateTime;
use IntlDateFormatter;
use Locale;
use Zend\I18n\Exception;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper for formatting dates.
 */
class DateFormat extends \Zend\I18n\View\Helper\DateFormat
{
    const YEAR_ONLY = 20;
    private $date_format = array(
        -1 => '',
        0 => 'l,d F Y',
        1 => 'd F Y',
        2 => 'd M Y',
        3 => 'd/m/Y',
        4 => 'Y/m/d',
        20 => 'Y'
    );

    private $time_format = array(
        -1 => '',
        0 => 'h:i:sa',
        1 => 'H:i:s',
        2 => 'h:ia',
        3 => 'H:i'
    );

    /**
     * @var \Localization\jCalendar
     */
    private $calendar;

    /**
     * Format a date
     *
     * @param  DateTime|int|array $date
     * @param  int $dateType
     * @param  int $timeType
     * @param  string $locale
     * @param  string|null $pattern
     * @return string
     */
    public function __invoke(
        $date,
        $dateType = 0,
        $timeType = -1,
        $locale = null,
        $pattern = null
    )
    {
        if ($this->getLocale() == 'fa_IR') {
            if ($pattern == null)
                $pattern = $this->date_format[$dateType] . ' ' . $this->time_format[$timeType];

            if ($dateType == 10 || $dateType == 3)
                $this->getCalendar()->farsiDigits = false;
            $string = trim($this->getCalendar()->date($pattern, $date, 0));
            $this->getCalendar()->farsiDigits = true;

        } else {

            if ($dateType == 20)
                $pattern = 'y';

            $date_object = new DateTime();
            $date_object->setTimestamp($date);
            $string = parent:: __invoke(
                $date_object,
                $dateType,
                $timeType,
                $locale,
                $pattern
            );
        }
        return $string;
    }

    /**
     * @return \Localization\jCalendar
     */
    public function getCalendar()
    {
        if (!$this->calendar)
            $this->calendar = new \Localization\jCalendar();
        return $this->calendar;
    }
}