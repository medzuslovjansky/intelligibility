<?php

declare(strict_types=1);

namespace Intelligibility\CoreBundle\Util\Helper;

use DateTime;
use Intelligibility\CoreBundle\Util\LocaleService;
use Symfony\Contracts\Translation\TranslatorInterface;

class IntelligibilityDateTimeHelper
{
    protected TranslatorInterface $translator;

    protected LocaleService $localeService;

    protected string $charset = 'UTF-8';

    public function __construct(TranslatorInterface $translator, LocaleService $localeService)
    {
        $this->translator = $translator;
        $this->localeService = $localeService;
    }

    /**
     * @param \Datetime|string|int $datetime
     * @param string|null          $locale
     * @param string|null timezone
     * @param mixed|null $timezone
     *
     * @return string
     */
    public function formatDateShortTimeShort($datetime, $locale = null, $timezone = null)
    {
        $date = $this->getDatetime($datetime, $timezone);

        $formatter = new \IntlDateFormatter(
            $locale ?: $this->localeService->getCurrentLocale(),
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::SHORT,
            $timezone ?: $date->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN
        );

        return $this->process($formatter, $date);
    }

    /**
     * @param \Datetime|string|int $datetime
     * @param string|null          $locale
     * @param string|null timezone
     * @param mixed|null $timezone
     *
     * @return string
     */
    public function formatDateShort($datetime, $locale = null, $timezone = null)
    {
        $date = $this->getDatetime($datetime, $timezone);

        $formatter = new \IntlDateFormatter(
            $locale ?: $this->localeService->getCurrentLocale(),
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            $timezone ?: $date->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN
        );

        return $this->process($formatter, $date);
    }

    /**
     * @param \Datetime|string|int $date
     * @param string|null          $locale
     * @param string|null timezone
     * @param mixed|null $timezone
     *
     * @return string
     */
    public function formatDateFullTimeShort($date, $locale = null, $timezone = null)
    {
        $datetime = $this->getDatetime($date, $timezone);

        $formatter = new \IntlDateFormatter(
            $locale ?: $this->localeService->getCurrentLocale(),
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::SHORT,
            $timezone ?: $datetime->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN
        );

        return $this->process($formatter, $datetime);
    }

    /**
     * @param \Datetime|string|int $date
     * @param string|null          $locale
     * @param string|null timezone
     * @param mixed|null $timezone
     *
     * @return string
     */
    public function formatDateLong($date, $locale = null, $timezone = null)
    {
        $dateTime = $this->getDatetime($date, $timezone);

        $formatter = new \IntlDateFormatter(
            $locale ?: $this->localeService->getCurrentLocale(),
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            $timezone ?: $dateTime->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN
        );

        return $this->process($formatter, $dateTime);
    }

    public function formatDateMonthAndYear($date, $locale = null, $timezone = null)
    {
        $dateTime = $this->getDatetime($date, $timezone);

        $formatter = new \IntlDateFormatter(
            $locale ?: $this->localeService->getCurrentLocale(),
            \IntlDateFormatter::LONG,
            \IntlDateFormatter::NONE,
            $timezone ?: $dateTime->getTimezone()->getName(),
            \IntlDateFormatter::GREGORIAN
        );
        $formatter->setPattern('MMMM yyyy');

        return $this->process($formatter, $dateTime);
    }

    public function getDatetime($data, $timezone = 'UTC'): DateTime
    {
        if ($data instanceof \DateTime) {
            return $data;
        }

        if ($data instanceof \DateTimeImmutable) {
            return \DateTime::createFromFormat(\DateTime::ATOM, $data->format(\DateTime::ATOM));
        }

        if (is_numeric($data)) {
            $data = (int) $data;
        }

        if (\is_string($data)) {
            $data = strtotime($data);
        }

        $date = new \DateTime();
        $date->setTimestamp($data);
        $date->setTimezone(new \DateTimeZone($timezone));

        return $date;
    }

    /**
     * NEXT_MAJOR: Change to $date to \DateTimeInterface.
     *
     * @return string
     */
    public function process(\IntlDateFormatter $formatter, DateTime $date)
    {
        // strange bug with PHP 5.3.3-7+squeeze14 with Suhosin-Patch
        // getTimestamp() method alters the object...
        return $this->fixCharset($formatter->format((int) $date->format('U')));
    }

    /**
     * Fixes the charset by converting a string from an UTF-8 charset to the
     * charset of the kernel.
     *
     * Precondition: the kernel charset is not UTF-8
     *
     * @param string $string The string to fix
     *
     * @return string A string with the %kernel.charset% encoding
     */
    protected function fixCharset($string)
    {
        if ('UTF-8' !== $this->getCharset()) {
            $string = mb_convert_encoding($string, $this->getCharset(), 'UTF-8');
        }

        return $string;
    }

    /**
     * @param string               $alias
     * @param \Datetime|string|int $date
     * @param string|null          $locale
     * @param string|null timezone
     * @param mixed|null $timezone
     *
     * @return string
     */
    public function formatByAlias($alias, $date, $locale = null, $timezone = null)
    {
        $method = 'format'.ucfirst($alias);
        if (method_exists($this, $method)) {
            return $this->$method($date, $locale, $timezone);
        }
        throw new \InvalidArgumentException(sprintf('The alias "%s", not exists!', $alias));
    }

    /**
     * Sets the default charset.
     *
     * @param string $charset The charset
     */
    public function setCharset($charset): void
    {
        $this->charset = $charset;
    }

    /**
     * Gets the default charset.
     *
     * @return string The default charset
     */
    public function getCharset()
    {
        return $this->charset;
    }
}
