<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Log\LoggerInterface;
use TYPO3\CMS\Core\TypoScript\FrontendTypoScript;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class LabelHelper
{
    public function __construct(
        private readonly LoggerInterface $logger,
    ) {}

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
        $label = LocalizationUtility::translate($id, $extensionName, $arguments);

        if ($label === null) {
            $this->logger->warning('Label with id "' . $id . '" could not be loaded. Extension "' . ($extensionName ?? 'N/A') . '".');
        }

        return $label ?: null;
    }

    private function isFrontendTypoScriptAvailable(): bool
    {
        $frontendTypoScript = $this->getRequest()?->getAttribute('frontend.typoscript');
        if (!$frontendTypoScript instanceof FrontendTypoScript) {
            return false;
        }
        return $frontendTypoScript->hasSetup();
    }

    private function getRequest(): ?ServerRequestInterface
    {
        return $GLOBALS['TYPO3_REQUEST'] ?? null;
    }
}
