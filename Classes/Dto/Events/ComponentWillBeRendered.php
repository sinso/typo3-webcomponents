<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\ComponentRenderingDataInterface;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Event is triggered after the component data has been provided and before the component is rendered.
 * Listeners can modify the component rendering data.
 * Listeners can throw an \Sinso\Webcomponents\DataProviding\AssertionFailedException to stop the rendering process.
 */
class ComponentWillBeRendered
{
    public function __construct(
        public readonly ContentObjectRenderer $contentObjectRenderer,
        public readonly ComponentRenderingDataInterface $componentRenderingData,
    ) {}
}
