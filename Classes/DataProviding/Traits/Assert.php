<?php

namespace Sinso\Webcomponents\DataProviding\Traits;

use Sinso\Webcomponents\DataProviding\AssertionFailedException;

/**
 * Use assertions in your web component to skip rendering if a condition is not met.
 * If $GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] is enabled, a comment with the assertion message will be put in the output.
 */
trait Assert
{
    protected function assert(bool $condition, string $message): void
    {
        if ($condition) {
            return;
        }
        throw new AssertionFailedException('Webcomponent ' . self::class . ' skipped rendering because of failed assertion: ' . $message, 1729064010);
    }
}
