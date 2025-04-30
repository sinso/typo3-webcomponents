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
     * You can pass your own ContentObjectRenderer instance if you need to, but for the sake of simplicity, you can just omit it.
     *
     * @throws UnableToLinkException
     */
    public function createLinkResult(string|int $linkParameter, ?string $linkText = '', ?ContentObjectRenderer $contentObjectRenderer = null): LinkResultInterface
    {
        $contentObjectRenderer = $contentObjectRenderer ?? $this->contentObjectRenderer;

        $linkResult = $this->linkFactory->create((string)$linkText, ['parameter' => $linkParameter], $contentObjectRenderer);
        if ($linkResult->getLinkText() !== null && $this->htmlSanitizationIsActive($contentObjectRenderer)) {
            // HTML sanitization encodes the link text, so we need to decode it to pass clean data to web components
            $linkResult = $linkResult->withLinkText(html_entity_decode((string)$linkResult->getLinkText()));
        }
        return $linkResult;
    }

    private function htmlSanitizationIsActive(ContentObjectRenderer $contentObjectRenderer): bool
    {
        $configuration = $contentObjectRenderer->mergeTSRef(
            ['parseFunc' => '< lib.parseFunc'],
            'parseFunc'
        );
        return !empty($configuration['parseFunc.']['htmlSanitize']) && $configuration['parseFunc.']['htmlSanitize'] === '1';
    }
}
