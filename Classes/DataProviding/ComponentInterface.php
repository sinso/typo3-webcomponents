<?php

namespace Sinso\Webcomponents\DataProviding;

use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\InputData;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

interface ComponentInterface
{
    public function provide(InputData $inputData): ComponentRenderingData;
}
