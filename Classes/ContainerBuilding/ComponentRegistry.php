<?php

declare(strict_types=1);

namespace Sinso\Webcomponents\ContainerBuilding;

class ComponentRegistry
{
    /**
     * @var array<ComponentRegistryEntry>
     */
    private array $componentRegistryEntries = [];

    /**
     * @param iterable<int, string> $cTypes
     */
    public function registerComponent(string $serviceClassName, iterable $cTypes): void
    {
        $componentRegistryEntry = new ComponentRegistryEntry($serviceClassName);
        foreach ($cTypes as $cType) {
            $componentRegistryEntry->addCType($cType);
        }
        $this->componentRegistryEntries[] = $componentRegistryEntry;
    }

    /**
     * @return iterable<ComponentRegistryEntry>
     */
    public function getComponentRegistryEntries(): iterable
    {
        return $this->componentRegistryEntries;
    }
}
