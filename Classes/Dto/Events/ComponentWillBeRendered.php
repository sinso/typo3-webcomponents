<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Event is triggered after the component data has been provided and before the component is rendered.
 * Listeners can modify the component rendering data.
 * Listeners can throw an \Sinso\Webcomponents\DataProviding\AssertionFailedException to stop the rendering process.
 */
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
