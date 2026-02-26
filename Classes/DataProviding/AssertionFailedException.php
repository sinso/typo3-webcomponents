<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding;

class AssertionFailedException extends \RuntimeException
{
    public function getRenderingPlaceholder(): string
    {
        /** @var array{FE?: array{debug?: bool}} $confVars */
        $confVars = $GLOBALS['TYPO3_CONF_VARS'] ?? [];
        if ($confVars['FE']['debug'] ?? false) {
            return '<!-- ' . $this->getMessage() . ' -->';
        }
        return '';
    }
}
