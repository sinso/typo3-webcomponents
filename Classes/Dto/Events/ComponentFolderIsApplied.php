<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Dto\Events;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

readonly class ComponentFolderIsApplied
{
    public function __construct(
        public ComponentRenderingData $componentRenderingData,
        public ContentObjectRenderer $contentObjectRenderer,
        public string $componentFolder,
    ) {
    }
}