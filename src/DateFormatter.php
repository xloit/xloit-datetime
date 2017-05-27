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

use DateTime as PhpDateTime;
use DateTimeZone;

/**
 * A {@link DateFormatter} class.
 *
 * @package Xloit\DateTime
 */
class DateFormatter
{
    /**
     * Specific timezone in which date should be formatted.
     *
     * @var DateTimeZone
     */
    protected static $timezone;

    /**
     *
     *
     * @var string
     */
    protected static $defaultFormat = PhpDateTime::ATOM;

    /**
     *
     *
     * @return string
     */
    public static function getDefaultFormat()
    {
        return static::$defaultFormat;
    }

    /**
     *
     *
     * @param string $format
     *
     * @return void
     */
    public static function setDefaultFormat($format)
    {
        static::$defaultFormat = $format;
    }

    /**
     *
     *
     * @param DateTime|string $dateTime
     * @param string          $format
     *
     * @return string
     * @throws \InvalidArgumentException
     * @throws \Xloit\DateTime\Exception\InvalidArgumentException
     */
    public static function format($dateTime, $format = null)
    {
        if (!($dateTime instanceof PhpDateTime)) {
            $dateTime = new DateTime($dateTime);
        }

        $currentDateTime = DateTime::instance($dateTime);

        if ($format === null && $dateTime instanceof DateTime) {
            $format = DateTime::DEFAULT_TO_STRING_FORMAT;
        }

        if ($format === null) {
            $format = static::$defaultFormat;
        }

        $currentDateTime->setTimezone(static::getTimezone());

        return $currentDateTime->format($format);
    }

    /**
     *
     *
     * @return DateTimeZone
     * @throws \Xloit\DateTime\Exception\InvalidArgumentException
     */
    public static function getTimezone()
    {
        if (!static::$timezone) {
            static::setTimezone(date_default_timezone_get());
        }

        return static::$timezone;
    }

    /**
     *
     *
     * @param DateTimeZone|string $timezone
     *
     * @return void
     * @throws \Xloit\DateTime\Exception\InvalidArgumentException
     */
    public static function setTimezone($timezone)
    {
        self::$timezone = static::safeCreateDateTimeZone($timezone);
    }

    /**
     * Creates a DateTimeZone from a string or a DateTimeZone.
     *
     * @link  \Carbon\Carbon::safeCreateDateTimeZone($object);
     *
     * @param DateTimeZone|string|null $timezone
     *
     * @return DateTimeZone
     * @throws \Xloit\DateTime\Exception\InvalidArgumentException
     */
    public static function safeCreateDateTimeZone($timezone)
    {
        if ($timezone === null) {
            // Don't return null...
            return new DateTimeZone(date_default_timezone_get());
        }

        if ($timezone instanceof DateTimeZone) {
            return $timezone;
        }

        /** @noinspection PhpUsageOfSilenceOperatorInspection */
        $tz = @timezone_open((string) $timezone);

        if ($tz === false) {
            throw new Exception\InvalidArgumentException(
                sprintf(
                    'Unknown or bad timezone (%s)', is_scalar($timezone) ? $timezone : gettype($timezone)
                )
            );
        }

        return $tz;
    }
}
