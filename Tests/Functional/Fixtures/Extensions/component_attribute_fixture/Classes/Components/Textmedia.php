<?php

declare(strict_types=1);

namespace Sinso\ComponentAttributeFixture\Components;

use Sinso\Webcomponents\Attribute\ComponentForContentElement;
use Sinso\Webcomponents\DataProviding\ComponentInterface;
use Sinso\Webcomponents\Dto\ComponentRenderingData;
use Sinso\Webcomponents\Dto\InputData;

#[ComponentForContentElement(cType: 'textmedia')]
final class Textmedia implements ComponentInterface
{
    public function provide(InputData $inputData): ComponentRenderingData
    {
        return (new ComponentRenderingData())
            ->withTagName('fixture-textmedia')
            ->withTagProperty('headline', $inputData->record['header']);
    }
}
