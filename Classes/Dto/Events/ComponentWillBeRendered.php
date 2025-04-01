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
final class ComponentWillBeRendered
{
    /**
     * @deprecated Do not access this property directly anymore, but use the getter and setter methods.
     */
    public ComponentRenderingData $componentRenderingData;

    public function __construct(
        ComponentRenderingData $componentRenderingData,
        public readonly ContentObjectRenderer $contentObjectRenderer,
    ) {
        $this->componentRenderingData = $componentRenderingData;
    }

    public function getComponentRenderingData(): ComponentRenderingData
    {
        return $this->componentRenderingData;
    }

    public function setComponentRenderingData(ComponentRenderingData $componentRenderingData): void
    {
        $this->componentRenderingData = $componentRenderingData;
    }
}
