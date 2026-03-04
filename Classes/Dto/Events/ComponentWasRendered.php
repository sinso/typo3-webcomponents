<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

final class ComponentWasRendered
{
    public function __construct(
        private string $markup,
        public readonly ComponentRenderingData $componentRenderingData,
        public readonly ContentObjectRenderer $contentObjectRenderer,
        public readonly string $componentClassName,
    ) {}

    public function getMarkup(): string
    {
        return $this->markup;
    }

    public function setMarkup(string $markup): void
    {
        $this->markup = $markup;
    }
}
