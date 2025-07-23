<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use Psr\Http\Message\ServerRequestInterface;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class LabelHelper
{
    /**
     * @param (string|int)[] $arguments
     */
    public function translate(?string $extensionName, string $id, array $arguments = []): ?string
    {
        if (!$this->isFrontendTypoScriptAvailable() && !empty($extensionName)) {
            // If Frontend TypoScript is not available, extensionName must not be used - because LocalizationUtility will try to load overwriting labels from TypoScript
            $id = 'LLL:EXT:' . $extensionName . '/Resources/Private/Language/locallang.xlf:' . $id;
            $extensionName = null;
        }
        return LocalizationUtility::translate($id, $extensionName, $arguments) ?: null;
    }

    private function isFrontendTypoScriptAvailable(): bool
    {
        return $this->getRequest()?->getAttribute('frontend.typoscript') instanceof FrontendTypoScript;
    }

    private function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
