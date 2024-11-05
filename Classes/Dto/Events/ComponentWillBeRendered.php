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
    public function __construct(
        public readonly ComponentRenderingData $componentRenderingData,
        public readonly ContentObjectRenderer $contentObjectRenderer,
    ) {}
}
