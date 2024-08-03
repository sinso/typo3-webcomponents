<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\Registry;

/**
 * Holds the components registered via
 * \Sinso\Webcomponents\Attribute\ComponentForContentElement.
 *
 * The map is compiled into the container by
 * \Sinso\Webcomponents\ContainerBuilding\CompilerPass\ComponentPass.
 */
final class ComponentRegistry
{
    /**
     * @param array<string, class-string> $componentsByCType
     */
    public function __construct(
        private readonly array $componentsByCType = [],
    ) {}

    /**
     * @return array<string, class-string>
     */
    public function getComponentsByCType(): array
    {
        return $this->componentsByCType;
    }
}
