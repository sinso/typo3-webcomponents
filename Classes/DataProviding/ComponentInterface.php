<?php

namespace Sinso\Webcomponents\DataProviding;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

interface ComponentInterface
{
    public function provide(ComponentRenderingData $componentRenderingData): ComponentRenderingData;

    public function setContentObjectRenderer(ContentObjectRenderer $contentObjectRenderer): void;
}
