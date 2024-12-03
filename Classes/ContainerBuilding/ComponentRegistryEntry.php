<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContainerBuilding;

class ComponentRegistryEntry
{
    /**
     * @var array<string> $cTypes
     */
    private array $cTypes = [];

    public function __construct(
        public readonly string $componentClassname,
    ) {}

    public function addCType(string $cType): void
    {
        $this->cTypes[] = $cType;
    }

    /**
     * @return iterable<string>
     */
    public function getCTypes(): iterable
    {
        return $this->cTypes;
    }
}
