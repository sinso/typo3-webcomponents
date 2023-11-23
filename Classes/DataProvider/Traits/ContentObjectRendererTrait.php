<?php

namespace Sinso\Webcomponents\DataProvider\Traits;

use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

trait ContentObjectRendererTrait
{
    protected ContentObjectRenderer $contentObjectRenderer;

    public function setContentObjectRenderer(ContentObjectRenderer $contentObjectRenderer): void
    {
        $this->contentObjectRenderer = $contentObjectRenderer;
    }
}
