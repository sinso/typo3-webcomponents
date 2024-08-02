<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ComponentWillBeRendered
{
    public function __construct(
        private readonly ContentObjectRenderer  $contentObjectRenderer,
        private readonly ComponentRenderingData $componentRenderingData,
    ) {
    }

    public function getContentObjectRenderer(): ContentObjectRenderer
    {
        return $this->contentObjectRenderer;
    }

    public function getComponentRenderingData(): ComponentRenderingData
    {
        return $this->componentRenderingData;
    }
}
