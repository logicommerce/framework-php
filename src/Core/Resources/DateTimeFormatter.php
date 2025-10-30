<?php

namespace FWK\Core\Resources;

use SDK\Core\Resources\Date;
use SDK\Application;
use FWK\Core\Exceptions\CommerceException;

/**
 * Date class to manage dates.
 *
 * @see DateTime::start()
 *
 * @package FWK\Core\Resources
 */
class DateTimeFormatter {

    private static string $locale = '';

    private static ?\DateTimeZone $timezone = null;

    private static ?\IntlCalendar $calendar = null;

    private static bool $init = false;

    private int $dateType = DATE_TIME_FORMATER_DEFAULT_DATE_TYPE;

    private int $timeType = DATE_TIME_FORMATER_DEFAULT_TIME_TYPE;

    private int $calendarType = DATE_TIME_FORMATER_DEFAULT_CALENDAR_TYPE;

    /**
     * Initialize the object, sets the locale value and timezone
     *
     * @param string $locale
     *
     * @return void
     */
    public static function init(string $locale): void {
        self::setLocale($locale);
        self::setTimezone();
        self::setCalendar();
        self::$init = true;
    }

    /**
     * Constructor.
     *
     * @param null|int $dateType
     * @param null|int $timeType
     * @param null|int $calendarType
     */

    public function __construct($dateType = null, $timeType = null, $calendarType = null) {
        if (self::$init) {
            if (!is_null($dateType)) {
                $this->dateType = $dateType;
            }
            if (!is_null($timeType)) {
                $this->timeType = $timeType;
            }
            if (!is_null($calendarType)) {
                $this->calendarType = $timeType;
            }
        } else {
            throw new CommerceException("DateTimeFormatter isn't initialized. See DateTimeFormatter::init()", CommerceException::DATE_TIME_FORMATTER_INIT_REQUIRED);
        }
    }

    private static function setLocale(string $locale): void {
        self::$locale = $locale;
    }

    private static function setTimezone(): void {
        self::$timezone = new \DateTimeZone(Application::getInstance()->getEcommerceSettings()->getGeneralSettings()->getTimeZone());
    }

    /**
     * Returns the timeZone
     *
     * @return ?\DateTimeZone
     */
    public static function getTimezone(): ?\DateTimeZone {
        return self::$timezone;
    }

    private static function setCalendar(): void {
        self::$calendar = \IntlCalendar::createInstance(self::$timezone, self::$locale);
    }

    /**
     * Returns the calendar
     *
     * @return ?\IntlCalendar
     */
    public static function getCalendar(): ?\IntlCalendar {
        return self::$calendar;
    }

    /**
     * Returns the IntlDateFormatter with the setted formats
     * 
     * @param null|string $pattern
     * @param bool $showTime
     *
     * @return \IntlDateFormatter
     */
    public function getFormatter($pattern = null, bool $showTime = true): \IntlDateFormatter {
        $timeType = $this->timeType;
        if (!$showTime) {
            $timeType = \IntlDateFormatter::NONE;
        }
        return datefmt_create(self::$locale, $this->dateType, $timeType, self::$timezone, $this->calendarType, $pattern);
    }

    /**
     * Returns the given date as string into the setted locale with the setted formats
     *
     * @param null|Date|DateTime $dateTime
     * @param null|string $pattern
     *
     * @return string
     */
    public function getFormattedDateTime($dateTime = null, $pattern = null): string {
        if (is_null($dateTime)) {
            $dateTime = new \DateTime('now', self::$timezone);
        } elseif ($dateTime instanceof Date) {
            $dateTime = $dateTime->getDateTime();
        } elseif (!$dateTime instanceof \DateTime) {
            throw new \InvalidArgumentException();
        }
        return datefmt_format($this->getFormatter($pattern), $dateTime);
    }

    /**
     * Returns the given date as string into the setted locale with the setted formats
     *
     * @param null|Date|DateTime $dateTime
     * @param null|string $pattern
     *
     * @return string
     */
    public function getFormattedDate($dateTime = null, $pattern = null): string {
        if (is_null($dateTime)) {
            $dateTime = new \DateTime('now', self::$timezone);
        } elseif ($dateTime instanceof Date) {
            $dateTime = $dateTime->getDateTime();
        } elseif (!$dateTime instanceof \DateTime) {
            throw new \InvalidArgumentException();
        }
        return datefmt_format($this->getFormatter($pattern, false), $dateTime);
    }


    /**
     * Returns the pattern date as string with the setted formats
     *
     * @return string
     */
    public function getPattern(): string {
        return datefmt_get_pattern($this->getFormatter());
    }
}
