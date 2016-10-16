<?php
/**
 * This source file is part of Xloit project.
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the MIT License that is bundled with this package in the file LICENSE.
 * It is also available through the world-wide-web at this URL:
 * <http://www.opensource.org/licenses/mit-license.php>
 * If you did not receive a copy of the license and are unable to obtain it through the world-wide-web,
 * please send an email to <license@xloit.com> so we can send you a copy immediately.
 *
 * @license   MIT
 * @link      http://xloit.com
 * @copyright Copyright (c) 2016, Xloit. All rights reserved.
 */

namespace Xloit\DateTime;

use Carbon\Carbon;
use DateTime as PhpDateTime;
use DateTimeZone;

/**
 * A {@link DateTime} class.
 *
 * @package Xloit\DateTime
 */
class DateTime extends Carbon
{
    /**
     * Number of seconds in a minute.
     *
     * @var int
     */
    const MINUTE = 60;

    /**
     * Number of seconds in an hour.
     *
     * @var int
     */
    const HOUR = 3600;

    /**
     * Number of seconds in a day.
     *
     * @var int
     */
    const DAY = 86400;

    /**
     * Number of seconds in a week.
     *
     * @var int
     */
    const WEEK = 604800;

    /**
     * Average number of seconds in a month.
     *
     * @var int
     */
    const MONTH = 2629744;

    /**
     * Average number of seconds in a year.
     *
     * @var int
     */
    const YEAR = 31556926;

    /**
     * Format to use for __toString method when type juggling occurs.
     *
     * @var string
     */
    protected $format = self::DEFAULT_TO_STRING_FORMAT;

    /**
     * Returns new Time object according to the specified DOS timestamp.
     *
     * @param int                      $timestamp DOS timestamp
     * @param DateTimeZone|string|null $timezone  (optional) A valid time zone or a DateTimeZone object
     *
     * @return static
     */
    public static function createFromDOSTimestamp($timestamp, $timezone = null)
    {
        $year      = (($timestamp >> 25) & 0x7f) + 1980;
        $mon       = ($timestamp >> 21) & 0x0f;
        $mday      = ($timestamp >> 16) & 0x1f;
        $hours     = ($timestamp >> 11) & 0x1f;
        $minutes   = ($timestamp >> 5) & 0x3f;
        $seconds   = 2 * ($timestamp & 0x1f);
        $timestamp = mktime($hours, $minutes, $seconds, $mon, $mday, $year);

        return static::createFromTimestamp($timestamp, $timezone);
    }

    /**
     * Returns a list of time zones where the key is a valid PHP time zone while the value is a presentable name.
     *
     * @return array
     */
    public static function getTimeZones()
    {
        $timezones = [];

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            $timezones[$timezone] = str_replace('_', ' ', $timezone);
        }

        return $timezones;
    }

    /**
     * Returns an array of grouped time zones where the key is a valid PHP timezone while the value is a presentable
     * name.
     *
     * @return array
     */
    public static function getGroupedTimeZones()
    {
        $timezones = [];

        foreach (DateTimeZone::listIdentifiers() as $timezone) {
            list($group, $city) = explode('/', $timezone, 2) + [
                null,
                null
            ];

            $timezones[$group][$timezone] = str_replace('_', ' ', $city);
        }

        unset($timezones['UTC']);

        return $timezones;
    }

    /**
     * Constructor to prevent {@link DateTime} from being loaded more than once.
     *
     * @param PhpDateTime|string|null  $time
     * @param DateTimeZone|string|null $timezone
     *
     * @throws \InvalidArgumentException
     */
    public function __construct($time = null, $timezone = null)
    {
        if ($timezone && !($timezone instanceof DateTimeZone)) {
            $timezone = static::safeCreateDateTimeZone($timezone);
        }

        if ($time instanceof PhpDateTime) {
            if (!$timezone) {
                $timezone = $time->getTimezone();
            }

            $time = $time->format('Y-m-d H:i:s.u');
        }

        parent::__construct($time, $timezone);
    }

    /**
     *
     *
     * @return string
     */
    public function getFormat()
    {
        return $this->format;
    }

    /**
     *
     *
     * @param string $format
     *
     * @return static
     */
    public function setFormat($format)
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Returns date formatted according to given format.
     *
     * @link   http://php.net/manual/en/datetime.format.php
     *
     * @param string $format
     *
     * @return string
     */
    public function format($format = null)
    {
        if (!$format) {
            $format = $this->getFormat();
        }

        return parent::format($format ?: static::$toStringFormat);
    }

    /**
     * Move forward in time by x seconds.
     *
     * @param int $seconds Number of seconds
     *
     * @return static
     */
    public function forward($seconds)
    {
        $this->addSeconds($seconds);

        return $this;
    }

    /**
     * Move backward in time by x seconds.
     *
     * @param int $seconds Number of seconds
     *
     * @return static
     */
    public function rewind($seconds)
    {
        $this->subSeconds($seconds);

        return $this;
    }

    /**
     * Returns the number of days in the current month.
     *
     * @return int
     */
    public function daysInMonth()
    {
        $days = [
            31,
            $this->isLeapYear() ? 29 : 28,
            31,
            30,
            31,
            30,
            31,
            31,
            30,
            31,
            30,
            31
        ];

        return $days[$this->format('n') - 1];
    }

    /**
     * Returns the DOS timestamp.
     *
     * @return int
     */
    public function getDOSTimestamp()
    {
        $time = getdate($this->getTimestamp());

        if ($time['year'] < 1980) {
            $time['year']    = 1980;
            $time['mon']     = 1;
            $time['mday']    = 1;
            $time['hours']   = 0;
            $time['minutes'] = 0;
            $time['seconds'] = 0;
        }

        return (($time['year'] - 1980) << 25)
               | ($time['mon'] << 21)
               | ($time['mday'] << 16)
               | ($time['hours'] << 11)
               | ($time['minutes'] << 5)
               | ($time['seconds'] >> 1);
    }

    /**
     * Format the instance as a string using the set format.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->format();
    }
}
