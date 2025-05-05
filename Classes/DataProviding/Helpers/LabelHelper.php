<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class LabelHelper
{
    /**
     * @param (string|int)[] $arguments
     */
    public function translate(string $extensionName, string $id, array $arguments = []): ?string
    {
        return LocalizationUtility::translate($id, $extensionName, $arguments) ?: null;
    }
}
