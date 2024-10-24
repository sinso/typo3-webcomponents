<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContainerBuilding\Attribute;

#[\Attribute(\Attribute::TARGET_CLASS)]
class ComponentForContentElements
{
    public const TAG_NAME = 'webcomponents.forContentElements';

    public function __construct(
        public readonly string $cType,
    ) {}
}
