<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\DataProviding\Helpers;

use TYPO3\CMS\Core\Resource\FileReference;
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
     * The link text of the result is plain text. Without a $linkText, TYPO3 generates one from the link target.
     * Don't cast the result to string or call getHtml(): both write the link text into the <a> tag unescaped.
     * You can pass your own ContentObjectRenderer instance if you need to, but for the sake of simplicity, you can just omit it.
     *
     * @throws UnableToLinkException
     */
    public function createLinkResult(string|int $linkParameter, ?string $linkText = '', ?ContentObjectRenderer $contentObjectRenderer = null): LinkResultInterface
    {
        $contentObjectRenderer = $contentObjectRenderer ?? $this->contentObjectRenderer;

        $linkText = (string)$linkText;
        $linkResult = $this->linkFactory->create($linkText, ['parameter' => $linkParameter], $contentObjectRenderer);
        if ($linkText === '' && $linkResult->getLinkText() !== null) {
            // TYPO3 HTML-encodes the link text it generates for most link types (htmlspecialchars() or lib.parseFunc)
            $linkResult = $linkResult->withLinkText(html_entity_decode($linkResult->getLinkText()));
        }
        return $linkResult;
    }

    /**
     * @throws UnableToLinkException
     */
    public function createLinkResultFromFileReference(FileReference $fileReference, ?ContentObjectRenderer $contentObjectRenderer = null): LinkResultInterface
    {
        return $this->createLinkResult(
            't3://file?uid=' . $fileReference->getOriginalFile()->getUid(),
            $fileReference->getTitle() ?: $fileReference->getOriginalFile()->getName(),
            $contentObjectRenderer,
        );
    }
}
