<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

class ComponentFolderIsApplied
{
    public function __construct(
        public readonly ComponentRenderingData $componentRenderingData,
        public readonly ContentObjectRenderer $contentObjectRenderer,
        public string $componentFolder,
    ) {}
}
