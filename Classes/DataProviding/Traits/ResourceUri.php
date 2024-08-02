<?php

namespace Sinso\Webcomponents\DataProviding\Traits;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Core\Utility\PathUtility;

trait ResourceUri
{
    protected function loadResourceUri(string $extensionName, string $path): string
    {
        $uri = sprintf(
            'EXT:%s/Resources/Public/%s',
            GeneralUtility::camelCaseToLowerCaseUnderscored($extensionName),
            $path,
        );
        return PathUtility::getAbsoluteWebPath(GeneralUtility::getFileAbsFileName($uri));
    }
}
