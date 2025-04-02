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
        return $this->linkFactory->create('', ['parameter' => $linkParameter], $this->contentObjectRenderer);
    }
}
