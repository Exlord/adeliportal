<?php
/**
 * Created by PhpStorm.
 * User: Exlord (adeli.farhad@gmail.com)
 * Date: 12/10/13
 * Time: 10:42 AM
 */

namespace Localization\API;


use Localization\jCalendar;

class Date
{
    /**
     * @var jCalendar
     */
    private static $calendar = null;

    private static $locale = null;

    /**
     * @return jCalendar
     */
    public static function getCalendar()
    {
        if (!self::$calendar) {
            self::$calendar = new jCalendar();
            self::$calendar->farsiDigits = false;
        }
        return self::$calendar;
    }

    private static function getLocale()
    {
        if (!self::$locale)
            self::$locale = \Locale::getDefault();
        return self::$locale;
    }

    /**
     * @param string $date A Jalali Date String Like : dd/mm/yyyy
     * @return int
     */
    public static function jalali_to_gregorian($date)
    {
        $date = explode("/", $date);
        $date = self::getCalendar()->jalali_to_gregorian($date[2], $date[1], $date[0]);
        return mktime(0, 0, 0, $date[1], $date[2], $date[0]);
    }

    public static function mkTime($hour = null, $minute = null, $second = null, $month = null, $day = null, $year = null)
    {
        if (self::getLocale() == 'fa_IR')
            return self::getCalendar()->mktime($hour, $minute, $second, $month, $day, $year);
        else
            return mktime($hour, $minute, $second, $month, $day, $year);
    }

    public static function date($format, $stamp = null)
    {
        if (self::getLocale() == 'fa_IR')
            return self::getCalendar()->date($format, $stamp, 0);
        else
            return date($format, $stamp);
    }

    /**
     * Formats a time interval with the requested granularity.
     *
     * @param int $interval The length of the interval in seconds.
     * @param int $granularity How many different units to display in the string.
     * @param null $langcode Optional language code to translate to a language other than
     *   what is used to display the page.
     * @return string A translated string representation of the interval.
     */
    public static function formatInterval($interval, $granularity = 2, $langcode = NULL)
    {
        if (!$interval)
            return t('Right Now');
        $units = array(
            '1 year|%s years' => 31536000,
            '1 month|%s months' => 2592000,
            '1 week|%s weeks' => 604800,
            '1 day|%s days' => 86400,
            '1 hour|%s hours' => 3600,
            '1 min|%s min' => 60,
            '1 sec|%s sec' => 1
        );
        $output = '';
        foreach ($units as $key => $value) {
            $key = explode('|', $key);
            if ($interval >= $value) {
                $output .= ($output ? ' ' : '') . tp($key[0], $key[1], floor($interval / $value));
                $interval %= $value;
                $granularity--;
            }

            if ($granularity == 0) {
                break;
            }
        }
        return $output ? $output : t('0 sec');
    }

    public static function fromDatePicker($date)
    {
        if (SYSTEM_LANG == 'fa')
            $date = Date::getCalendar()->jalali_to_gregorian($date[0], $date[1], $date[2]);

        $y = $date[0];
        $mo = $date[1];
        $d = $date[2];

        return mktime(0, 0, 0, $mo, $d, $y);
    }

    public static function toDatePicker($date)
    {
        if (SYSTEM_LANG == 'fa') {
            return Date::getCalendar()->date('Y/m/d', $date);
        } else {
            return date('Y/m/d', $date);
        }
    }
} 