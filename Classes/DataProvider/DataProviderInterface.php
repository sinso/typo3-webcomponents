<?php

namespace Sinso\Webcomponents\DataProvider;

use Sinso\Webcomponents\Dto\WebcomponentRenderingData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

interface DataProviderInterface
{
    public function provide(WebcomponentRenderingData $webcomponentRenderingData): WebcomponentRenderingData;

    public function setContentObjectRenderer(ContentObjectRenderer $contentObjectRenderer): void;
}
