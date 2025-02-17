<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

class ResourceUriHelper
{
    public function loadResourceUri(string $extensionName, string $path): string
    {
        $uri = sprintf(
            'EXT:%s/Resources/Public/%s',
            GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName),
            $path,
        );
        return PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName($uri));
    }
}
