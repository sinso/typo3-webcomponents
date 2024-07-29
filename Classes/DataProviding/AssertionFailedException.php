<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding;

class AssertionFailedException extends \RuntimeException
{
    public function getRenderingPlaceholder(): string
    {
        if ($GLOBALS['TYPO3_CONF_VARS']['FE']['debug'] ?? false) {
            return '<!-- ' . $this->getMessage() . ' -->';
        }
        return '';
    }
}
