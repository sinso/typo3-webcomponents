<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;
use TYPO3\CMS\Frontend\Typolink\LinkFactory;
use TYPO3\CMS\Frontend\Typolink\LinkResultInterface;
use TYPO3\CMS\Frontend\Typolink\UnableToLinkException;

class LinkHelper
{
    public function __construct(
        private readonly ContentObjectRenderer $contentObjectRenderer,
        private readonly LinkFactory $linkFactory,
    ) {}

    /**
     * @throws UnableToLinkException
     */
    public function createLinkResult(string|int $linkParameter): LinkResultInterface
    {
        $linkResult = $this->linkFactory->create('', ['parameter' => $linkParameter], $this->contentObjectRenderer);
        if ($linkResult->getLinkText() !== null && $this->htmlSanitizationIsActive()) {
            // HTML sanitization encodes the link text, so we need to decode it to pass clean data to web components
            $linkResult = $linkResult->withLinkText(html_entity_decode((string)$linkResult->getLinkText()));
        }
        return $linkResult;
    }

    private function htmlSanitizationIsActive(): bool
    {
        $configuration = $this->contentObjectRenderer->mergeTSRef(
            ['parseFunc' => '< lib.parseFunc'],
            'parseFunc'
        );
        return !empty($configuration['parseFunc.']['htmlSanitize']) && $configuration['parseFunc.']['htmlSanitize'] === '1';
    }
}
