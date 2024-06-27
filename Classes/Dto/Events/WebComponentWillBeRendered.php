<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class WebComponentWillBeRendered
{
    public function __construct(
        private readonly ContentObjectRenderer $contentObjectRenderer,
        private readonly WebcomponentRenderingData $webcomponentRenderingData,
    ) {
    }

    public function getContentObjectRenderer(): ContentObjectRenderer
    {
        return $this->contentObjectRenderer;
    }

    public function getWebcomponentRenderingData(): WebcomponentRenderingData
    {
        return $this->webcomponentRenderingData;
    }
}
