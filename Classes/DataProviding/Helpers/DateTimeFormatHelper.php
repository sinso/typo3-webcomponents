<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\Localization\Locale;

class DateTimeFormatHelper
{
    /**
     * @param int $dateType See IntlDateFormatter constants
     * @param int $timeType See IntlDateFormatter constants
     */
    public function formatTime(\DateTimeInterface|int $timestamp, int $dateType = \IntlDateFormatter::LONG, int $timeType = \IntlDateFormatter::NONE): ?string
    {
        $locale = $this->getLocaleFromRequest()?->getName() ?? \Locale::getDefault();
        $formatter = new \IntlDateFormatter($locale, $dateType, $timeType);

        return $formatter->format($timestamp) ?: null;
    }

    private function getLocaleFromRequest(): ?Locale
    {
        return $this->getRequest()->getAttribute('language')?->getLocale();
    }

    private function getRequest(): ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'];
    }
}
