<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\InputData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class ComponentEvaluated
{
    /**
     * @deprecated Do not access this property directly anymore, but use the getter and setter methods.
     */
    public ComponentRenderingData $componentRenderingData;

    /**
     * @param class-string<ComponentInterface> $componentClassName
     */
    public function __construct(
        ComponentRenderingData $componentRenderingData,
        public readonly ContentObjectRenderer $contentObjectRenderer,
        public readonly InputData $inputData,
        public readonly string $componentClassName,
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
