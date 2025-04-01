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
     * @param class-string<ComponentInterface> $componentClassName
     */
    public function __construct(
        public readonly ComponentRenderingData $componentRenderingData,
        public readonly ContentObjectRenderer $contentObjectRenderer,
        public readonly InputData $inputData,
        public readonly string $componentClassName,
    ) {}
}
